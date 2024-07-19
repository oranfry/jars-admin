<?php

namespace jars\admin;

use jars\contract\Constants;

class AdminRouter extends \subsimple\Router
{
    protected static $routes = [
        // ajax

        'POST /ajax/login' => [
            'AUTHSCHEME' => 'none',
            'LAYOUT' => 'json',
            'PAGE' => 'jars/admin/ajax/login',
        ],

        'POST /ajax/save' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'json',
            'PAGE' => 'jars/admin/ajax/save',
        ],

        // login

        'GET /' => [
            'AUTHSCHEME' => 'none',
            'LAYOUT' => 'jars/admin/centered',
            'PAGE' => 'jars/admin/frontend/login',
        ],

        // logout

        'GET /logout' => [
            'AUTHSCHEME' => 'none',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/logout',
        ],

        // report

        'GET /report/([a-z]+)' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/report',
            'LINETYPE_NAME' => null,
            'LINE_ID' => null,
            'GROUP_NAME' => '',
            0 => 'REPORT_NAME',
        ],

        'GET /report/([a-z]+)/(' . Constants::GROUP_PATTERN  . ')' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/report',
            'LINETYPE_NAME' => null,
            'LINE_ID' => null,
            0 => 'REPORT_NAME',
            1 => 'GROUP_NAME',
        ],

        'GET /report/([a-z]+):([a-z]+)/([a-zA-Z0-9-]+)' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/report',
            'GROUP_NAME' => '',
            0 => 'REPORT_NAME',
            1 => 'LINETYPE_NAME',
            2 => 'LINE_ID',
        ],

        'GET /report/([a-z]+)/(' . Constants::GROUP_PATTERN  . '):([a-z]+)/([a-zA-Z0-9-]+)' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/report',
            0 => 'REPORT_NAME',
            1 => 'GROUP_NAME',
            2 => 'LINETYPE_NAME',
            3 => 'LINE_ID',
        ],

        'GET /raw/([a-z]+)/(' . Constants::GROUP_PATTERN  . ')' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/raw',
            0 => 'REPORT_NAME',
            1 => 'GROUP_NAME',
            'LINETYPE_NAME' => null,
            'LINE_ID' => null,
        ],

        'GET /raw/([a-z]+)/(' . Constants::GROUP_PATTERN  . '):([a-z]+)/([a-zA-Z0-9-]+)' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/raw',
            0 => 'REPORT_NAME',
            1 => 'GROUP_NAME',
            2 => 'LINETYPE_NAME',
            3 => 'LINE_ID',
        ],

        // download

        'GET /download/([a-z]+)/([a-zA-Z0-9-]+)' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/file',
            'PAGE' => 'jars/admin/frontend/download',
            0 => 'TABLE_NAME',
            1 => 'RECORD_ID',
        ],
   ];
}
