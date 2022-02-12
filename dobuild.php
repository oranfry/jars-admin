#!/usr/bin/php
<?php

define('APP_HOME', __DIR__);

const SUBSIMPLE_HOME = APP_HOME . '/vendor/oranfry/subsimple';
const APICLIENT_HOME = APP_HOME . '/vendor/oranfry/jars-client';
const JARS_CORE_HOME = APP_HOME . '/vendor/oranfry/jars-core';

require APP_HOME . '/vendor/autoload.php';
require SUBSIMPLE_HOME . '/build.php';
