<?php

$api = new ApiClient(AUTH_TOKEN, APIURL);

return ['data' => $api->logout()];
