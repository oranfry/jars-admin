<?php if ($download): ?>
    <div style="padding: 0 2em 0 0; position: relative;">
        <a class="download-button" data-for="<?= $name ?>" data-table="<?= $download->table ?>" download style="position:absolute; right: 0; top: 8px" <?= @$value ? null : 'disabled' ?>>
            <i class="icon icon--gray icon--<?= $download->icon ?? 'doc' ?>"></i>
        </a>
<?php endif ?>
<?php if (@$options): ?>
    <select name="<?= $name ?>" style="width: 80%">
        <?php foreach ($options as $k => $v): ?>
            <?php
                $_value = $v;
                $_label = is_numeric($k) ? $v : $k;
            ?>
            <option <?= $_value == @$value ? 'selected="selected"' : '' ?>><?= $_label ?></option>
        <?php endforeach ?>
    </select>
    <button type="button" class="adhoc-toggle">&hellip;</button>
<?php elseif (@$multiline): ?>
    <textarea class="field value" name="<?= $name ?>"></textarea>
<?php else: ?>
    <input class="field value" type="text" name="<?= $name ?>" autocomplete="off">
<?php endif ?>
<?php if ($download): ?>
    </div>
<?php endif ?>
