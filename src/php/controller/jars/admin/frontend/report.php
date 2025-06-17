<?php

use \jars\Report;
use jars\admin\Helper;
use jars\contract\Constants;
use obex\Obex;

$reports = $jars->reports();

if (!$report = Obex::find($reports, 'name', 'is', REPORT_NAME)) {
    header('Location: ' . HOMEPATH);
    die();
}

$min = @$_GET['version'];

$groups = [];
$path = [];

foreach (explode('/', GROUP_NAME . (GROUP_NAME ? '/' : null) . 'fake') as $part) {
    $prefix = ($prefix = implode('/', $path)) ? $prefix . '/' : '';
    $groups[] = array_filter($jars->groups(REPORT_NAME, $prefix, $min), fn ($g) => $g !== '');
    $path[] = $part;
}

array_pop($path);

$fields = $report->fields;

if (GROUP_NAME && 0 <= $index = count($groups) - 2) {
    $last_groups = $groups[$index];

    if (!in_array(basename(GROUP_NAME), $last_groups)) {
        header('Location: ' . BASEPATH . '/report/' . REPORT_NAME);

        die();
    }
}

$linetypes = $jars->linetypes(REPORT_NAME);
$title = REPORT_NAME;

if (GROUP_NAME) {
    $title .= ' ' . GROUP_NAME;
}

$data = $jars->group(REPORT_NAME, GROUP_NAME, @$min);
$base_version = $jars->version();

if ($jars->report(REPORT_NAME)->is_derived()) {
    return compact(
        'base_version',
        'data',
        'groups',
        'jars',
        'path',
        'reports',
        'title',
    );
}

$lines = &$data;
$pos = array_search(GROUP_NAME, $groups);
$prevGroup = $pos !== false && $pos > 0 ? $groups[$pos - 1] : null;
$nextGroup = $pos !== false && $pos < count($groups) - 1 ? $groups[$pos + 1] : null;
$childpath = Helper::parseChildPath($jars, CHILDPATH, LINETYPE_NAME, LINE_ID, $lines, $linetypes);

return compact(
    'base_version',
    'childpath',
    'fields',
    'groups',
    'jars',
    'lines',
    'linetypes',
    'nextGroup',
    'path',
    'prevGroup',
    'reports',
    'title',
);
