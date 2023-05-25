<?php use obex\Obex; ?>
<div class="sidebar" id="sidebar" data-area-margin="15" data-area-maxwidth="150">
    <nav>
        <?php foreach ($reports as $report): ?>
            <a
                href="/report/<?= $report->name ?>"
                <?php if (PAGE == 'jars/admin/frontend/report' && REPORT_NAME == $report->name): ?>class="current"<?php endif ?>
            ><?= $report->name ?></a>
        <?php endforeach ?>
    </nav>
</div>
<?php if (count($groups) > 1 || count($groups) && reset($groups) !== 'all'): ?>
    <div class="sidebar" id="sidebar" data-area-margin="15" data-area-maxwidth="150">
        <nav>
            <?php foreach ($groups as $group_name): ?>
                <a
                    href="/report/<?= REPORT_NAME ?>/<?= $group_name ?>"
                    <?php if (PAGE == 'jars/admin/frontend/report' && GROUP_NAME == $group_name): ?>class="current"<?php endif ?>
                ><?= $group_name ?></a>
            <?php endforeach ?>
        </nav>
    </div>
<?php endif ?>
<div class="area list-area" id="list-area">
    <?php if (count(@$warnings ?: [])): ?>
        <div class="warnings">
            <?php foreach ($warnings as $warning): ?>
                <div class="warning"><?= $warning ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?php if (isset($lines)): ?>
        <div class="easy-table" style="width: 100%">
            <div class="easy-table__row easy-table__row--header">
                <div class="easy-table__cell" class="select-column printhide"><i class="icon icon--gray icon--smalldot-o selectall"></i></div>
                <?php foreach ($fields as $field): ?>
                    <div class="easy-table__cell <?= $field->type == 'number' ? 'easy-table__cell--right' : '' ?>"><?= $field->name ?></div>
                <?php endforeach ?>
                <div class="easy-table__cell" class="extend"></div>
            </div>
            <?php foreach ($lines as $i => $line): ?>
                <div
                    class="easy-table__row linerow"
                    <?php if (@$line->id): ?>data-id="<?= $line->id ?>"<?php endif ?>
                    <?php if (@$line->type) : ?>data-type="<?= $line->type ?>"<?php endif ?>
                >
                    <div class="easy-table__cell select-column printhide"><input type="checkbox"></div>
                    <?php foreach ($fields as $field): ?>
                        <div
                            class="easy-table__cell list-value"
                            data-name="<?= $field->name ?>"
                            style="max-width: <?= bcdiv(90, count($fields), 2) ?>%; <?php if ($field->type == 'number'): ?>text-align: right;<?php endif ?>"
                        ><div class="limitedwidth"><?= htmlspecialchars($line->{$field->name} ?? '') ?></div></div>
                    <?php endforeach ?>
                    <div class="easy-table__cell extend limitedwidth">
                        <p><?= @$line->type ?></p>
                        <span style="display: none;">
                            <?php if ($linetype = Obex::find($linetypes, 'name', 'is', @$line->type)): ?>
                                <?php foreach ($linetype->fields as $field): ?>
                                    <input type="hidden" name="<?= $field->name ?>" value="<?= htmlspecialchars($line->{$field->name} ?? '') ?>">
                                <?php endforeach ?>
                            <?php endif ?>
                            <br>
                            <br>
                            <textarea class="raw"><?= htmlspecialchars(json_encode($line, JSON_PRETTY_PRINT)) ?></textarea>
                        </span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <div style="text-align:center; color: #999;"><br><?= count($lines) ?> lines</div>
    <?php endif ?>
    <?php if (isset($data)): ?>
        <textarea class="raw fullarea"><?= htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) ?></textarea>
    <?php endif ?>
    <?php if (@$linetypes): ?>
        <nav>
            <?php foreach ($linetypes as $linetype): ?>
                <a
                    href="#"
                    class="trigger-add-line"
                    data-type="<?= $linetype->name ?>"
                >+ <?= $linetype->name ?></a>
            <?php endforeach ?>
        </nav>
    <?php endif ?>
</div>
<?php if (@$linetypes): ?>
    <div class="area editor-area" id="editor-area">
        <?php foreach ($linetypes as $linetype): ?>
            <div data-type="<?= $linetype->name ?>" class="line floatline edit-form" style="display: none">
                <?php $value = null; ?>
                <form method="post">
                    <div class="form-row">
                        <div class="form-row__label">&nbsp;</div>
                        <div class="form-row__value">
                            <h3><?= $linetype->name ?></h3>
                        </div>
                    </div>
                    <?php foreach ($linetype->fields as $field_details) : ?>
                        <?php $name = $field_details->name; ?>
                        <?php $multiline = $field_details->multiline; ?>
                        <?php $download = $field_details->downloadable ? (object) [
                            'icon' => $field_details->download_icon,
                            'table' => $field_details->download_table,
                        ] : null; ?>
                        <?php $fieldsType = $field_details->type; ?>
                        <?php $inc = search_plugins("src/php/partial/fieldtype/{$fieldsType}.php"); ?>
                        <?php if (!file_exists($inc)) : ?>
                            <?php error_response("Unsupported field type: {$fieldsType}"); ?>
                        <?php endif ?>
                        <div class="form-row">
                            <div class="form-row__label"><?= $field_details->name ?></div>
                            <div class="form-row__value"><?php require $inc; ?></div>
                            <div style="clear: both"></div>
                        </div>
                    <?php endforeach ?>
                    <div class="form-row">
                        <div class="form-row__label">&nbsp;</div>
                        <div class="form-row__value"><button class="saveline button button--main" type="button">Save</button> <button class="deleteline button button--main" type="button">Delete</button></div>
                        <div style="clear: both"></div>
                        <br>
                    </div>
                </form>
                <form method="post">
                    <div>
                        <textarea name="raw" class="raw"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-row__label">&nbsp;</div>
                        <div class="form-row__value"><button class="savelineraw button button--main" type="button">Save</button> <button class="deleteline button button--main" type="button">Delete</button></div>
                        <div style="clear: both"></div>
                        <br>
                    </div>
                </form>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
