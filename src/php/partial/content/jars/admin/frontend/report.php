<?php

use obex\Obex;

?><script>let base_version = '<?= $base_version ?>';</script><?php

?><div class="sidebar" id="sidebar" data-area-margin="15" data-area-maxwidth="150"><?php
    ?><nav><?php
        foreach ($reports as $report) {
            ?><a<?php

            ?> href="/report/<?= $report->name ?>"<?php

            if (PAGE == 'jars/admin/frontend/report' && REPORT_NAME == $report->name) {
                ?> class="current"<?php
            }

            ?>><?php

            echo $report->name

            ?></a><?php
        }
    ?></nav><?php
?></div><?php

$pieces = [];

foreach ($groups as $i => $groupset) {
    $selected = preg_replace(',/$,', '', $path[$i] ?? '');

    if (count($groupset)) {
        ?><div class="sidebar" id="sidebar" data-area-margin="15" data-area-maxwidth="150"><?php
            ?><nav><?php
                foreach ($groupset as $group_name) {
                    ?><a<?php
                    ?> href="/report/<?= REPORT_NAME ?>/<?= ($prefix = implode('/', $pieces)) ? $prefix . '/' : null ?><?= $group_name ?>"<?php

                    if ($group_name == $selected){
                        ?> class="current"<?php
                    }

                    ?>><?php

                    echo $group_name;

                    ?></a><?php
                }
            ?></nav><?php
        ?></div><?php
    }

    $pieces[] = $selected;
}

?><div class="area list-area" id="list-area"><?php
    if (count(@$warnings ?: [])) {
        ?><div class="warnings"><?php
            foreach ($warnings as $warning) {
                ?><div class="warning"><?= $warning ?></div><?php
            }
        ?></div><?php
    }

    if (isset($lines)) {
        ?><div class="easy-table" style="width: 100%"><?php
            ?><div class="easy-table__row easy-table__row--header"><?php
                ?><div class="easy-table__cell" class="select-column printhide"><i class="icon icon--gray icon--smalldot-o selectall"></i></div><?php

                foreach ($fields as $field){
                    ?><div class="easy-table__cell <?= $field->type == 'number' ? 'easy-table__cell--right' : '' ?>"><?= $field->name ?></div><?php
                }
                ?><div class="easy-table__cell" class="extend"></div><?php
            ?></div><?php

            foreach ($lines as $i => $line) {
                ?><div<?php
                ?> class="easy-table__row linerow"<?php

                if (@$line->id) {
                    ?> data-id="<?= $line->id ?>"<?php
                }

                if (@$line->type) {
                    ?> data-type="<?= $line->type ?>"<?php
                }

                ?>><?php
                    ?><div class="easy-table__cell select-column printhide"><input type="checkbox"></div><?php

                    foreach ($fields as $field) {
                        ?><div<?php
                        ?> class="easy-table__cell list-value"<?php
                        ?> data-name="<?= $field->name ?>"<?php
                        ?> style="max-width: <?= bcdiv(90, count($fields), 2) ?>%; <?php if ($field->type == 'number'): ?>text-align: right;<?php endif ?>"<?php
                        ?>><?php
                            ?><div class="limitedwidth"><?= htmlspecialchars($line->{$field->name} ?? '') ?></div><?php
                        ?></div><?php
                    }

                    ?><div class="easy-table__cell extend limitedwidth"><?php
                        ?><p><?= @$line->type ?></p><?php

                        ?><span style="display: none;"><?php
                            if ($linetype = Obex::find($linetypes, 'name', 'is', @$line->type)) {
                                foreach ($linetype->fields as $field) {
                                    ?><input type="hidden" name="<?= $field->name ?>" value="<?= htmlspecialchars($line->{$field->name} ?? '') ?>"><?php
                                }
                            }
                        ?></span><?php
                    ?></div><?php
                ?></div><?php
            }
        ?></div><?php
        ?><div style="text-align:center; color: #999;"><br><?= count($lines) ?> lines</div><?php
    }

    if (isset($data)) {
        ?><textarea class="raw fullarea"><?= htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></textarea><?php
    }

    if (@$linetypes) {
        ?><nav><?php
            foreach ($linetypes as $linetype) {
                ?><a<?php
                ?> href="#"<?php
                ?> class="trigger-add-line"<?php
                ?> data-type="<?= $linetype->name ?>"<?php
                ?>><?php

                echo '+ ' . $linetype->name;

                ?></a><?php
            }
        ?></nav><?php
    }
?></div><?php

if (@$linetypes) {
    ?><div class="area editor-area" id="editor-area"><?php
        foreach ($linetypes as $linetype) {
            ?><div data-type="<?= $linetype->name ?>" class="line floatline edit-form" style="display: none"><?php
                $value = null;

                ?><form method="post"><?php
                    ?><div class="form-row"><?php
                        ?><div class="form-row__label">&nbsp;</div><?php
                        ?><div class="form-row__value"><?php
                            ?><h3><?= $linetype->name ?></h3><?php
                        ?></div><?php
                    ?></div><?php

                    foreach ($linetype->fields as $field) {
                        $name = $field->name;
                        $multiline = $field->multiline;
                        $dp = @$field->dp;

                        $download = ($field->downloadable ?? false) ? (object) [
                            'icon' => $field->download_icon,
                            'table' => $field->download_table,
                        ] : null;

                        ?><div class="form-row"><?php
                            ?><div class="form-row__label"><?php

                            echo $name;

                            ?></div><?php

                            ?><div class="form-row__value"><?php

                            ss_require(
                                "src/php/partial/fieldtype/$field->type.php",
                                compact('multiline', 'name', 'download', 'dp'),
                            );

                            ?></div><?php
                            ?><div style="clear: both"></div><?php
                        ?></div><?php
                    }
                    ?><div class="form-row"><?php
                        ?><div class="form-row__label">&nbsp;</div><?php
                        ?><div class="form-row__value"><?php
                            ?><button class="saveline button button--main" type="button">Save</button><?php
                            ?> <button class="deleteline button button--main" type="button">Delete</button><?php
                            ?> <button class="rawline button button--main" type="button">Raw</button><?php
                        ?></div><?php
                        ?><div style="clear: both"></div><?php
                        ?><br><?php
                    ?></div><?php
                ?></form><?php
            ?></div><?php
        }
    ?></div><?php
}
