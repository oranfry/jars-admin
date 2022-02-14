<?php

$message = null;

if ($jars->token()) {
    error_response('Unexpectedly, token is already present');
    die();
}

if (@$_COOKIE['token'] && $jars->verify_token($_COOKIE['token'])) {
    $jars->token($_COOKIE['token']);
    $config = $jars->config();

    list($first) = array_keys($config->reports);

    if (!$first) {
        error_response($config->reports);
    }

    header('Location: /report/' . $first);

    die('Redirecting...');
}

return [];
