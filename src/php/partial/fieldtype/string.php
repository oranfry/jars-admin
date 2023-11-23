<?php

if ($download) {
    ?><div style="padding: 0 2em 0 0; position: relative;"><?php
        ?><a<?php
        ?> class="download-button"<?php
        ?> data-for="<?= $name ?>"<?php
        ?> data-table="<?= $download->table ?>"<?php
        ?> download<?php
        ?> style="position:absolute; right: 0; top: 8px"<?php

        if (!@$value) {
            ?> disabled<?php
        }

        ?>><?php
            ?><i class="icon icon--gray icon--<?= $download->icon ?? 'doc' ?>"></i><?php
        ?></a><?php
}

if (@$options) {
    ?><select name="<?= $name ?>" style="width: 80%"><?php
        foreach ($options as $k => $v) {
            $_value = $v;
            $_label = is_numeric($k) ? $v : $k;
            ?><option <?= $_value == @$value ? 'selected="selected"' : '' ?>><?= $_label ?></option><?php
        }
    ?></select><?php
    ?><button type="button" class="adhoc-toggle">&hellip;</button><?php
} elseif (@$multiline) {
    ?><textarea class="field value" name="<?= $name ?>"></textarea><?php
} else {
    ?><input class="field value" type="text" name="<?= $name ?>" autocomplete="off"><?php
}

if ($download) {
    ?></div><?php
}
