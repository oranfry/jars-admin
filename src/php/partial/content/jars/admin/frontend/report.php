<?php use obex\Obex; ?>
<div class="sidebar" id="sidebar">
    <h3>reports</h3>
    <nav>
        <?php foreach ($reports as $report): ?>

            <a
                href="/report/<?= $report->name ?>"
                <?php if (PAGE == 'jars/admin/frontend/report' && REPORT_NAME == $report->name): ?>class="current"<?php endif ?>
            ><?php echo $report->name ?></a>

        <?php endforeach ?>
    </nav>

    <h3>groups</h3>
    <nav>
        <?php foreach ($groups as $group_name): ?>

            <a
                href="/report/<?= REPORT_NAME ?>/<?= $group_name ?>"
                <?php if (PAGE == 'jars/admin/frontend/report' && GROUP_NAME == $group_name): ?>class="current"<?php endif ?>
            ><?php echo $group_name ?></a>

        <?php endforeach ?>
    </nav>
</div>

<div class="areas">

    <div class="list-area" id="list-area">
        <?php if (count(@$warnings ?: [])): ?>
            <div class="warnings">
                <?php foreach ($warnings as $warning): ?>
                    <div class="warning"><?= $warning ?></div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if (isset($lines)): ?>

            <table class="easy-table">
                <thead>
                    <tr>
                        <th class="select-column printhide"><i class="icon icon--gray icon--smalldot-o selectall"></i></td></th>
                        <?php foreach ($fields as $field): ?>
                            <th
                                class="<?= $field->type == 'number' ? 'right' : '' ?>"
                            ><?= $field->name ?></th>
                        <?php endforeach ?>
                        <th class="extend"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lines as $i => $line): ?>
                        <tr
                            class="linerow"
                            <?php if (@$line->id): ?>data-id="<?= $line->id ?>"<?php endif ?>
                            <?php if (@$line->type) : ?>data-type="<?= $line->type ?>"<?php endif ?>
                        >
                            <td class="select-column printhide"><input type="checkbox"></td>
                            <?php foreach ($fields as $field): ?>
                                <?php $value = @$line->{$field->name}; ?>
                                <td
                                    class="list-value"
                                    data-name="<?= $field->name ?>"
                                    style="max-width: <?= bcdiv(90, count($fields), 2) ?>%; <?php if ($field->type == 'number'): ?>text-align: right;<?php endif ?>"
                                ><?= htmlspecialchars($value) ?></td>
                            <?php endforeach ?>
                            <td class="extend">
                                <p><?= @$line->type ?: '&nbsp;' ?></p>
                                <span style="display: none;">
                                    <?php if ($linetype = Obex::find($linetypes, 'name', 'is', @$line->type)): ?>
                                        <?php foreach ($linetype->fields as $field): ?>
                                            <input type="hidden" name="<?= $field->name ?>" value="<?= htmlspecialchars(@$line->{$field->name}) ?>">
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    <pre class="raw"><?= htmlspecialchars(json_encode($line, JSON_PRETTY_PRINT)) ?></pre>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <div style="text-align:center; color: #999;"><br><?= count($lines); ?> lines</div>
        <?php endif ?>

        <?php if ($linetypes): ?>
            <nav>
                <?php foreach ($linetypes as $linetype): ?>
                    <a
                        href="#"
                        class="trigger-add-line"
                        data-type="<?= $linetype->name ?>"
                    ><i class="icon icon--gray icon--plus"></i> <?= $linetype->name ?></a>
                <?php endforeach ?>
            </nav>
        <?php endif ?>
    </div>

    <div class="editor-area" id="editor-area">
        <?php foreach ($linetypes as $linetype): ?>
            <div data-type="<?php echo $linetype->name ?>" class="line floatline edit-form" style="display: none">
                <?php $value = null; ?>
                <form method="post">
                    <div class="form-row">
                        <div class="form-row__label">&nbsp;</div>
                        <div class="form-row__value">
                            <h3><?= $linetype->name ?></h3>
                        </div>
                    </div>

                    <?php
                        foreach ($linetype->fields as $field_details) {
                            $name = $field_details->name;
                            $multiline = $field_details->multiline;
                            $fieldsType = $field_details->type;
                            $inc = search_plugins("src/php/partial/fieldtype/{$fieldsType}.php");

                            if (!file_exists($inc)) {
                                error_response("Unsupported field type: {$fieldsType}");
                            }
                            ?>
                            <div class="form-row">
                                <div class="form-row__label"><?= $field_details->name ?></div>
                                <div class="form-row__value"><?php require $inc; ?></div>
                                <div style="clear: both"></div>
                            </div>
                            <?php
                        }
                    ?>

                    <div class="form-row">
                        <div class="form-row__label">&nbsp;</div>
                        <div class="form-row__value"><button class="saveline button button--main" type="button">Save</button> <button class="deleteline button button--main" type="button">Delete</button></div>
                        <div style="clear: both"></div>
                    </div>
                </form>
                <pre class="raw"></pre>
            </div>
        <?php endforeach ?>
    </div>
</div>