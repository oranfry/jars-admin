<?php

use OranFry\ContextVariableSets\ContextVariableSet;
use OranFry\ContextVariableSets\Value;
use OranFry\Jars\Admin\Helper;
use OranFry\Jars\Contract\Constants;
use OranFry\Jars\Core\Report;
use OranFry\Obex\Obex;
use OranFry\Tools\ContextVariableSets\GroupNavigator;

$baseUrl = implode('', EATENS);

$reportOptions = Obex::from($jars->reports())
    ->filter('is_derived', 'is', true);

if (!$reportOptions->count()) {
    return [
        'title' => 'No Derived Reports',
        'report' => null,
    ];
}

$reportSelector = new Value('report', [
    'options' => $reportOptions->map('name'),
    'nullable' => false,
    'label' => '',
    'manips' => 'path=',
]);

ContextVariableSet::put('report', $reportSelector);

$report = $reportOptions->find('name', 'is', $reportSelector->value) ?? $reportOptions->first();

$path = new GroupNavigator('path', [
    'jars' => $jars,
    'report' => $report->name,
]);

ContextVariableSet::put('path', $path);

$data = $jars->group($report->name, implode('/', $path->value), $_GET['version'] ?? null);
$base_version = $jars->version();

$title = $report->name;

return compact('base_version', 'data', 'title', 'report');
