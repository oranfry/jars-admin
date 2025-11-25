<!DOCTYPE html><?php

?><html lang="en-NZ"><?php

?><head><?php
    ?><meta name="viewport" content="width=320, initial-scale=1, user-scalable=no"><?php
    ?><link rel="stylesheet" type="text/css" href="/build/css/admin.<?= latest('admin-css') ?>.css"><?php
    ?><meta charset="utf-8"/><?php
    ?><title>Jars Admin &bull; <?= $title ?? PAGE ?></title><?php
?></head><?php

?><body<?php

if (isset($body_class)) {
    ?> class="<?= implode(' ', $body_class) ?>"<?php
}

?>><?php

    ss_require('src/php/partial/content/' . (defined('VIEW') ? VIEW : PAGE) . '.php', $viewdata);

    ?><script><?php
        ?>window.JARS_ADMIN_BASEPATH = '<?= JARS_ADMIN_BASEPATH ?>';<?php
        ?>window.JARS_ADMIN_HOMEPATH = '<?= JARS_ADMIN_HOMEPATH ?>';<?php

        if (defined('BACK') && BACK) {
            ?>window.BACK = '<?= BACK ?>';<?php
        }

        foreach (PAGE_PARAMS as $key => $value) {
            ?>window.<?= "{$key} = '{$value}'"; ?>;<?php
        }

        if ($jars->token()) {
            ?>window.orig_token = '<?= $jars->token() ?>';<?php
        }
    ?></script><?php

    ?><script type="text/javascript" src="/build/js/admin.<?= latest('admin-js') ?>.js"></script><?php

    ss_include('src/php/partial/js/' . PAGE . '.php', $viewdata);

?></body><?php
?></html><?php
