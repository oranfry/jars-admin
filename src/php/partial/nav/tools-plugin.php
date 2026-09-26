<?php

?><div class="navset"><?php
    ?><div class="listable"><?php
        ?><a href="<?= defined('TOOLS_BASE_URL') ? TOOLS_BASE_URL : null ?><?= $plugin->httpMountPoint ?>" <?= preg_replace(',/.*,', '', PAGE) == 'ledger' && defined('LEDGER_CONFIG') && LEDGER_CONFIG == 'report' ? 'class="current"' : ''?>>Primary</a><?php
        ?><a href="<?= defined('TOOLS_BASE_URL') ? TOOLS_BASE_URL : null ?><?= $plugin->httpMountPoint ?>/derived" <?= PAGE == 'jars/admin/derived' ? 'class="current"' : ''?>>Derived</a><?php
    ?></div><?php
?></div><?php
