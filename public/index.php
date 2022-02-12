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

$errors = [];

if (!$portal_home = @$host_config->portal_home ?? $_SERVER['PORTAL_HOME']) {
    $errors[] = 'Unable to determine PORTAL_HOME';
}

if (!$db_home = @$host_config->db_home ?? $_SERVER['DB_HOME']) {
    $errors[] = 'Unable to determine DB_HOME';
}

if ($errors) {
    error_response(implode('; ', $errors));
}

require APP_HOME . '/vendor/autoload.php';
require $portal_home . '/vendor/autoload.php';

require SUBSIMPLE_HOME . '/subsimple.php';
