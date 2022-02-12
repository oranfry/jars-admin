<?php

use \jars\Filesystem;
use \jars\Report;

$filesystem = new Filesystem();
$config = $jars->config($jars->token());
$reports = $config->reports;
$report = $jars->report(REPORT_NAME);
$min = @$_GET['version'];

$fields = [];

if (!$report) {
    header("Location: /");
    die();
}

$groups = $report->groups($min);

if (defined('GROUP_NAME') && !in_array(GROUP_NAME, $groups)) {
    header('Location: /report/' . REPORT_NAME);

    die();
}

if (!defined('GROUP_NAME') && @$groups[0]) {
    header('Location: /report/' . REPORT_NAME . '/' . $groups[0]);

    die();
}

foreach (@$config->report_fields[REPORT_NAME] ?? [(object) ['name' => 'id']] as $field) {
    if (is_string($field)) {
        $field = (object) ['name' => $field];
    }

    if (!isset($field->type)) {
        $field->type = 'string';
    }

    $fields[] = $field;
}

$linetypes = array_map(function ($name) use ($jars) {
    $linetype = $jars->linetype($name);

    $linetype->parent_fields = array_filter(map_objects(
        $linetype->find_incoming_links(),
        '@only_parent'
    ));

    return $linetype;
}, $report->linetypes());

$respect_newline_fields = [];

foreach ($linetypes as $linetype) {
    $respect_newline_fields[$linetype->name] = @$config->respect_newline_fields[$linetype->name] ?? [];
}

if (!defined('GROUP_NAME')) {
    $title = REPORT_NAME;

    return compact('jars', 'fields', 'groups', 'linetypes', 'reports', 'title', 'respect_newline_fields');
}

$lines = $report->get(GROUP_NAME, $min);
$title = REPORT_NAME . ' ' . GROUP_NAME;
$pos = array_search(GROUP_NAME, $groups);
$prevGroup = $pos !== false && $pos > 0 ? $groups[$pos - 1] : null;
$nextGroup = $pos !== false && $pos < count($groups) - 1 ? $groups[$pos + 1] : null;

return compact('jars', 'fields', 'groups', 'lines', 'linetypes', 'reports', 'title', 'prevGroup', 'nextGroup', 'respect_newline_fields');
