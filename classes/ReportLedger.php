<?php

namespace OranFry\Jars\Admin;

use OranFry\ContextVariableSets\ContextVariableSet;
use OranFry\ContextVariableSets\Value;
use OranFry\Jars\Contract\Client;
use OranFry\Jars\Core\Report;
use OranFry\Obex\Obex;
use OranFry\Ledger\Exception;
use OranFry\Tools\ContextVariableSets\GroupNavigator;
use OranFry\Tools\ContextVariableSets\ChildNavigator;

class ReportLedger extends \OranFry\Ledger\JarsAwareConfig
{
    protected ?GroupNavigator $path = null;
    protected ?ChildNavigator $childpath = null;
    protected ?Value $line = null;
    protected ?Value $reportSelector = null;
    protected array $fields = [];
    protected ?array $lines = null;
    protected array $linetypes = [];
    protected array $linetypeDetails = [];
    protected $raw = null;
    protected ?string $showasOverride = null;

    public function init(array $viewdata): void
    {
        parent::init($viewdata);

        $showasRaw = @$_GET['showas__value'] === 'raw';

        $_GET = [];

        $reports = $this->jars->reports();
        $reportNames = Obex::map($reports, 'name');
        $reportMetas = $this->jars->extra('reportMeta');
        $showable = fn ($report) => !$report->is_derived || ($reportMetas[$report->name]['treatAsLines'] ?? false);
        $reportOptions = Obex::map(array_filter($reports, $showable), 'name');

        if (REPORT_NAME) {
            if (!in_array(REPORT_NAME, $reportNames)) {
                throw new Exception('No such report');
            }

            if (!in_array(REPORT_NAME, $reportOptions)) {
                throw new Exception('That report is not one of the viewable reports');
            }
        }

        $_GET['report__value'] = REPORT_NAME;

        if (!$reportOptions) {
            return ;
        }

        $this->reportSelector = new Value('report', [
            'options' => $reportOptions,
            'nullable' => false,
            'label' => '',
            'manips' => 'path=&line=&childpath=',
        ]);

        $report = Obex::from($reports)->find('name', 'is', $this->reportSelector->value);

        if ($report->is_derived && $treatLike = $reportMetas[$report->name]['treatAsLines'] ?? null) {
            if (!in_array($treatLike, $reportNames)) {
                throw new (Exception('No such treat-like report'));
            }

            $report = Obex::from($reports)->find('name', 'is', $treatLike);

            if ($report->is_derived) {
                throw new (Exception('Treat-like report not supported'));
            }
        }

        foreach (explode('/', GROUP_NAME) as $i => $chunk) {
            $_GET['path__' . $i] = $chunk;
        }

        $this->path = new GroupNavigator('path', [
            'jars' => $this->jars,
            'report' => $this->reportSelector->value,
            'manips' => 'line=&childpath=',
        ]);

        $this->lines = $this->jars->group(
            $this->reportSelector->value,
            implode('/', $this->path->value),
            $this->version,
        );

        $this->base_version = $this->jars->version();

        $lineOptions = [''];

        if (LINE_ID) {
            $_GET['line__value'] = LINETYPE_NAME . '/' . LINE_ID;
            $lineOptions = [LINETYPE_NAME . '/' . substr(LINE_ID, 0, 6) => LINETYPE_NAME . '/' . LINE_ID];   
        }

        $this->line = new Value('line', [
            'options' => $lineOptions,
            'manips' => 'childpath=',
            'value' =>  LINE_ID ? LINETYPE_NAME . '/' . LINE_ID : null,
        ]);

        $this->linetypes = $this->jars->linetypes(
            $report->name,
        );

        $line = LINE_ID ? Obex::from($this->lines)
            ->filter('type', 'is', LINETYPE_NAME)
            ->find('id', 'is', LINE_ID) : null;

        $childpathPieces = explode('/', ltrim(CHILDPATH ?? '', '/'));

        for ($i = 0; $property = array_shift($childpathPieces); $i++) {
            $_GET['childpath__property_' . $i] = $property;

            if ($id = array_shift($childpathPieces)) {
                $_GET['childpath__id_' . $i] = $id;
            }
        }

        $reportMeta = $reportMetas[$report->name] ?? null;

        $this->childpath = new ChildNavigator('childpath', [
            'jars' => $this->jars,
            'report' => $report->name,
            'linetype_name' => LINETYPE_NAME,
            'line_id' => LINE_ID,
            'report_meta' => &$reportMeta,
            'lines' => &$this->lines,
            'linetypes' => &$this->linetypes,
        ]);

        foreach ($reportMetas[REPORT_NAME ?? '/']['fields'] ?? $reportMetas[$report->name]['fields'] ?? ['name' => 'id|start(6)', 'type' =>'string'] as $key => $field) {
            if (is_string($field)) {
                $field = ['name' => $field];
            }

            if (is_array($field)) {
                $field = (object) $field;
            }

            if (!isset($field->name) && is_string($key)) {
                $field->name = $key;
            }

            if (!@$field->type) {
                $field->type = 'string';
            }

            $this->fields[] = $field;

            $this->linetypeDetails = $report->linetypes;

            if ($showasRaw) {
                $onlyId = $this->childpath->value ? end($this->childpath->value)->id : LINE_ID;

                if ($onlyId) {
                    $this->lines = Obex::filter($this->lines, 'id', 'is', $onlyId);
                }
            }
        }

        if ($showasRaw) {
            $_GET['showas__value'] = 'raw';
        }
    }

    public function context(): ?object
    {
        return (object) [
            'line' => @$this->childpath->info ? $this->jars->flatten($this->childpath->context) : null,
            'childpath' => @$this->childpath->info,
        ];
    }

    public function download(string $linetype_name, string $line_id, ?string $field_name): ?object
    {
        if (null === $field_name) {
            throw new Exception('Unsupported');
        }

        $download = @$this->jars->extra('linetypeMeta')[$linetype_name]['fields'][$field_name]['download'];
        $record_table = @$download['table'];

        if (!$record_table) {
            throw new Exception('Could not determine record_table for download');
        }

        $line = $this->jars->get($linetype_name, $line_id);
        $record_id = @$line->$field_name;

        if (!$record_id) {
            throw new Exception('Could not determine record_id for download');
        }

        $file = $this->jars->record($record_table, $record_id, $content_type, $filename);

        return (object) compact('file', 'content_type', 'filename');
    }

    public function error(): ?string
    {
        return 'No Reports';
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function groupingInfo(): ?object
    {
        return (object) [
            'groupings' => [''],
        ];
    }

    public function hideTitle(): bool
    {
        return true;
    }

    public function lineGrouping(object $line): ?string
    {
        return '';
    }

    public function lines(): ?array
    {
        return $this->lines;
    }

    public function linetypeDetails(): array
    {
        return $this->linetypeDetails;
    }

    public function linetypes(): array
    {
        $linetypes = $this->linetypes;

        foreach ($linetypes as $linetype) {
            $linetypeMeta = $this->jars->extra('linetypeMeta')[$linetype->name] ?? [];

            foreach ($linetype->fields as $field) {
                $fieldMeta = $linetypeMeta['fields'][$field->name] ?? null;

                $field->multiline = $fieldMeta['multiline'] ?? false;
                $field->downloadable = isset($fieldMeta['download']);

                if ($field->downloadable) {
                    $download = $fieldMeta['download'];
                    $field->download_icon = @$download['icon'];
                 }

                if ($field->type === 'float') {
                    $field->dp = $fieldMeta['dp'] ?? 0;
                }
            }
        }

        return $linetypes;
    }

    public function save(array $data): array
    {
        $result = parent::save($data);

        if (
            defined('JARS_ADMIN_REFRESH_ON_SAVE')
            && JARS_ADMIN_REFRESH_ON_SAVE
        ) {
            $this->jars->refresh();
        }

        return $result;
    }

    public function showas(): array
    {
        if (!$this->reportSelector) {
            return ['list'];
        }

        return ['list', 'raw'];
    }

    public function showasOverride(): ?string
    {
        return $this->showasOverride;
    }

    public function title(): string
    {
        return 'Report &bull; ' . implode('/', array_filter([@$this->reportSelector->value, ...($this->path->value ?? [])]));
    }

    public function underTableItems(): array
    {
        $items = [
            (object) [
                'text' => count($this->lines) . ' lines',
            ]
        ];

        if (CHILDPATH) {
            $parentpath = array_slice($this->childpath->value, 0, count($this->childpath->value) - 1);
            $parentpath_r = implode('/', array_map(fn ($item) => $item->property . '/' . $item->id, $parentpath));
            $suffix = $parentpath_r ? '/' . $parentpath_r : null;

            $items[] = (object) [
                'text' => 'back',
                'href' => implode('', EATENS) . '/' . REPORT_NAME . '/' . GROUP_NAME . ':' . LINETYPE_NAME . '/' . LINE_ID . $suffix,
            ];
        }

        return $items;
    }

    public function variables(): array
    {
        return array_values(array_filter([
            $this->reportSelector,
            $this->path,
            $this->line,
            $this->childpath,
        ]));
    }
}