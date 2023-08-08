<?php

use \jars\Report;
use jars\contract\Constants;
use obex\Obex;

$reports = $jars->reports();

if (!$report = Obex::find($reports, 'name', 'is', REPORT_NAME)) {
    header("Location: /");
    die();
}

$min = @$_GET['version'];

$groups = [];
$path = [];

foreach (explode('/', GROUP_NAME . (GROUP_NAME ? '/' : null) . 'fake') as $part) {
    $prefix = ($prefix = implode('/', $path)) ? $prefix . '/' : '';
    $groups[] = $jars->groups(REPORT_NAME, $prefix, $min);
    $path[] = $part;
}

array_pop($path);

$fields = $report->fields;

if (GROUP_NAME && 0 <= $index = count($groups) - 2) {
    $last_groups = $groups[$index];

    if (!in_array(basename(GROUP_NAME), $last_groups)) {
        header('Location: /report/' . REPORT_NAME);

        die();
    }
}

$linetypes = $jars->linetypes(REPORT_NAME);

if (!GROUP_NAME) {
    $title = REPORT_NAME;

    return compact('jars', 'fields', 'groups', 'path', 'linetypes', 'reports', 'title');
}

$title = REPORT_NAME . ' ' . GROUP_NAME;

if ($jars->report(REPORT_NAME)->is_derived()) {
    $data = $jars->group(REPORT_NAME, GROUP_NAME);

    return compact('jars', 'data', 'groups', 'path', 'reports', 'title');
}

$lines = $jars->group(REPORT_NAME, GROUP_NAME, @$min);
$pos = array_search(GROUP_NAME, $groups);
$prevGroup = $pos !== false && $pos > 0 ? $groups[$pos - 1] : null;
$nextGroup = $pos !== false && $pos < count($groups) - 1 ? $groups[$pos + 1] : null;

return compact('jars', 'fields', 'groups', 'path', 'lines', 'linetypes', 'reports', 'title', 'prevGroup', 'nextGroup');
