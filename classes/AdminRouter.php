<?php

namespace jars\admin;

class AdminRouter extends \subsimple\Router
{
    protected static $routes = [
        // ajax
        'POST /(ajax/login)' => ['PAGE', 'AUTHSCHEME' => 'none', 'LAYOUT' => 'json'],
        'POST /(ajax/save)' => ['PAGE', 'AUTHSCHEME' => 'cookie', 'LAYOUT' => 'json'],

        // login / logout
        'GET /' => ['PAGE' => 'frontend/login', 'AUTHSCHEME' => 'none', 'LAYOUT' => 'centered'],
        'GET /logout' => ['PAGE' => 'frontend/logout', 'AUTHSCHEME' => 'none'],

        // report
        'GET /report/([a-z]+)' => ['REPORT_NAME', 'PAGE' => 'frontend/report', 'AUTHSCHEME' => 'cookie'],
        'GET /report/([a-z]+)/([a-zA-Z0-9-]+)' => ['REPORT_NAME', 'GROUP_NAME',  'PAGE' => 'frontend/report', 'AUTHSCHEME' => 'cookie'],
   ];
}
