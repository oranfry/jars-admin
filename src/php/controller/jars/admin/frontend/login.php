<?php

$message = null;

if ($jars->token()) {
    error_response('Unexpectedly, token is already present');
    die();
}

if ($token = @$_COOKIE['token']) {
    $jars->token($token);

    if ($jars->touch()) {
        $reports = $jars->reports();

        if (!$reports) {
            throw new Exception('No reports!');
        }

        header('Location: ' . BASEPATH . '/report/' . reset($reports)->name);

        die('Redirecting...');
    }
}

return [];
