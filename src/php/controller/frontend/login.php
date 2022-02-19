<?php

$message = null;

if ($jars->token()) {
    error_response('Unexpectedly, token is already present');
    die();
}

if ($token = @$_COOKIE['token']) {
    $jars->token($token);

    if ($jars->touch()) {
        list($first) = $jars->reports();

        if (!$first) {
            error_response('No reports!');
        }

        header('Location: /report/' . $first->name);

        die('Redirecting...');
    }
}

return [];
