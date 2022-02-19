<?php

return (object) [
    'jars' => $jars_config,
    'requires' => [
        SUBSIMPLE_HOME,
        JARS_CORE_HOME,
    ],
    'router' => 'jars\admin\AdminRouter',
];
