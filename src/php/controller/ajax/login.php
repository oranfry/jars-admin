<?php

$data = json_decode(file_get_contents('php://input'));

if (!$username = @$data->username) {
    error_response('Missing: username');
}

if (!$password = @$data->password) {
    error_response('Missing: username');
}

$token = $jars->login($username, $password);

if (!$token) {
    error_response('Invalid username / password');
}

return [
    'data' => (object) ['token' => $token],
];
