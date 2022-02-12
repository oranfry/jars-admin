<?php

use subsimple\Config;
use jars\Jars;

$jars_config = Config::get()->jars;
$jars = new Jars($jars_config->portal_home, $jars_config->db_home);

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
    if (!$token || !$jars->verify_token($token)) {
        setcookie('token', '', time() - 3600);
        header('Location: /');

        die();
    }

    $jars->token($token);
}

return compact('jars');
