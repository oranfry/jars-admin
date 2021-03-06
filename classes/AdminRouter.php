<?php

namespace jars\admin;

class AdminRouter extends \subsimple\Router
{
    protected static $routes = [
        // ajax

        'POST /ajax/login' => [
            'AUTHSCHEME' => 'none',
            'LAYOUT' => 'jars/admin/json',
            'PAGE' => 'jars/admin/ajax/login',
        ],

        'POST /ajax/save' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/json',
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
            0 => 'REPORT_NAME',
        ],

        'GET /report/([a-z]+)/([a-zA-Z0-9-]+)' => [
            'AUTHSCHEME' => 'cookie',
            'LAYOUT' => 'jars/admin/main',
            'PAGE' => 'jars/admin/frontend/report',
            0 => 'REPORT_NAME',
            1 => 'GROUP_NAME',
        ],
   ];
}
