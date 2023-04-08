<?php

use \jars\Report;
use obex\Obex;

$reports = $jars->reports();

if (!$report = Obex::find($reports, 'name', 'is', REPORT_NAME)) {
    header("Location: /");
    die();
}

$min = @$_GET['version'];
$groups = $jars->groups(REPORT_NAME, $min);
$fields = $report->fields;

if (defined('GROUP_NAME') && !in_array(GROUP_NAME, $groups)) {
    header('Location: /report/' . REPORT_NAME);

    die();
}

if (!defined('GROUP_NAME') && @$groups[0]) {
    header('Location: /report/' . REPORT_NAME . '/' . $groups[0]);

    die();
}

$linetypes = $jars->linetypes(REPORT_NAME);

if (!defined('GROUP_NAME')) {
    $title = REPORT_NAME;

    return compact('jars', 'fields', 'groups', 'linetypes', 'reports', 'title');
}

$title = REPORT_NAME . ' ' . GROUP_NAME;

if ($jars->report(REPORT_NAME)->is_derived()) {
    $data = $jars->group(REPORT_NAME, GROUP_NAME);

    return compact('jars', 'data', 'groups', 'reports', 'title');
}

$lines = $jars->group(REPORT_NAME, GROUP_NAME, @$min);
$pos = array_search(GROUP_NAME, $groups);
$prevGroup = $pos !== false && $pos > 0 ? $groups[$pos - 1] : null;
$nextGroup = $pos !== false && $pos < count($groups) - 1 ? $groups[$pos + 1] : null;

return compact('jars', 'fields', 'groups', 'lines', 'linetypes', 'reports', 'title', 'prevGroup', 'nextGroup');
