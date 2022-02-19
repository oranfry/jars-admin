<?php

use subsimple\Config;
use jars\Jars;
use jars\client\HttpClient;

$jars_config = Config::get()->jars;

if (@$jars_config->portal_home) {
    $jars = Jars::of($jars_config->portal_home, $jars_config->db_home);
} else {
    $jars = HttpClient::of($jars_config->jars_url);
}

$token = null;

switch (AUTHSCHEME) {
    case 'header':
        $token = @getallheaders()['X-Auth'];
        break;

    case 'cookie':
        $token = @$_COOKIE['token'];
        break;

    case 'none':
        break;

    default:
        error_response('Internal Server Error', 500);
}

if (in_array(AUTHSCHEME, ['header', 'cookie'])) {
    if (!$token) {
        header('Location: /');
        die();
    }

    $jars->token($token);

    if (!$jars->touch()) {
        setcookie('token', '', time() - 3600);
        header('Location: /');
        die();
    }
}

return compact('jars');
