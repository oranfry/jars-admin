<?php

use jars\Report;
use jars\contract\Constants;
use obex\Obex;

$reports = $jars->reports();

if (!$report = Obex::find($reports, 'name', 'is', REPORT_NAME)) {
    header("Location: /");
    die();
}

$data = $jars->group(REPORT_NAME, GROUP_NAME);

if (LINE_ID) {
    $data = Obex::from($data)
        ->filter('id', 'is', LINE_ID)
        ->find('type', 'is', LINETYPE_NAME);
}

$base_version = $jars->version();

$title = 'Raw Editor';
$back = preg_replace(',/raw/,', '/report/', $_SERVER['REQUEST_URI']);

return compact(
    'back',
    'base_version',
    'data',
    'title',
);
