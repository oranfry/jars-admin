<?php

use jars\Report;
use jars\contract\Constants;
use obex\Obex;

$reports = $jars->reports();

if (!$report = Obex::find($reports, 'name', 'is', REPORT_NAME)) {
    header("Location: /");
    die();
}

$line = Obex::from($jars->group(REPORT_NAME, GROUP_NAME))
    ->filter('id', 'is', LINE_ID)
    ->find('type', 'is', LINETYPE_NAME);

$title = 'Raw Editor';
$back = preg_replace(',/raw/,', '/report/', $_SERVER['REQUEST_URI']);

return compact('line', 'title', 'back');
