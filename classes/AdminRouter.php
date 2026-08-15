<?php

namespace OranFry\Jars\Admin;

use OranFry\Jars\Contract\Constants;

class AdminRouter extends \OranFry\Subsimple\Router
{
    const CHILDPATH_PATTERN = '(?:/[a-z]+/[a-f0-9]{64})*(?:/[a-z]+)?';

    protected static $routes = [
        // report

        'GET /' => [
            'FORWARD' => \OranFry\Ledger\Router::class,
            'LEDGER_CONFIG' => 'report',
            'REPORT_NAME' => null,
            'GROUP_NAME' => '',
            'LINETYPE_NAME' => null,
            'LINE_ID' => null,
            'CHILDPATH' => '',
        ],

        'GET /([a-z]+)(?:/(' . Constants::GROUP_PATTERN  . ')(?::([a-z]+)/([a-f0-9]{64})(' . self::CHILDPATH_PATTERN . '))?)?' => [
            'FORWARD' => \OranFry\Ledger\Router::class,
            'LEDGER_CONFIG' => 'report',
            0 => 'REPORT_NAME',
            1 => 'GROUP_NAME',
            2 => 'LINETYPE_NAME',
            3 => 'LINE_ID',
            4 => 'CHILDPATH',
        ],

        'POST /ajax/save' => [
            'FORWARD' => \OranFry\Ledger\Router::class,
            'LEDGER_CONFIG' => 'report',
        ],
   ];
}
