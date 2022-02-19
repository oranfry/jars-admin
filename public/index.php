<?php

define('APP_HOME', dirname(__DIR__));

const SUBSIMPLE_HOME = APP_HOME . '/vendor/oranfry/subsimple';
const APICLIENT_HOME = APP_HOME . '/vendor/oranfry/jars-client';
const JARS_CORE_HOME = APP_HOME . '/vendor/oranfry/jars-core';

$host_config = (object) [];

if (isset($_SERVER['HTTP_HOST']) && file_exists($config_file = __DIR__ . '/configs/' . $_SERVER['HTTP_HOST'] . '.php')) {
    $host_config = require $config_file;

    if (!is_object($host_config)) {
        error_response('Jars Admin: Host config file should return an object.');
    }
}

if ($portal_home = @$host_config->portal_home ?? @$_SERVER['PORTAL_HOME']) {
    if (!$db_home = @$host_config->db_home ?? @$_SERVER['DB_HOME']) {
        error_response('When setting PORTAL_HOME, please also set DB_HOME');
    }

    $jars_config = (object) [
        'db_home' => @$db_home,
        'portal_home' => @$portal_home,
    ];
} elseif ($jars_url = @$host_config->jars_url ?? @$_SERVER['JARS_URL']) {
    $jars_config = (object) [
        'jars_url' => @$jars_url,
    ];
} else {
    error_response('Please define PORTAL_HOME home or JARS_URL');
}

require APP_HOME . '/vendor/autoload.php';
require SUBSIMPLE_HOME . '/subsimple.php';
