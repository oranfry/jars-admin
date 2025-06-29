<?php

use obex\Obex;

echo '<script>';

?>let base_version = '<?= $base_version ?>';<?php
?>let childpath = <?= json_encode($childpath) ?>;<?php
?>let context_line = <?= json_encode($context) ?>;<?php

echo '</script>';

$menus = [];

$menus[] = array_map(function ($report) {
    return [
        'href' => BASEPATH . '/report/' . $report->name,
        'text' => $report->name,
        'current' => PAGE == 'jars/admin/frontend/report' && REPORT_NAME == $report->name,
    ];
}, $reports);

$pieces = [];

foreach ($groups as $i => $groupset) {
    $selected = preg_replace(',/$,', '', $path[$i] ?? '');

    if (count($groupset)) {
        $menu = [];

        foreach ($groupset as $group_name) {
            $text = $group_name;

            if (preg_match('/^[0-9a-f]{64}$/', $text)) {
                $text = substr($text, 0, 7);
            }

            $current = $group_name == $selected;
            $href = BASEPATH . '/report/' . REPORT_NAME . '/' . (($prefix = implode('/', $pieces)) ? $prefix . '/' : null) . $group_name;

            $menu[] = compact('href', 'text', 'current');

            if ($current) {
                $base_href = $href;
            }
        }

        $menus[] = $menu;
    }

    $pieces[] = $selected;
}

if (LINE_ID) {
    $base_href .= ':' . LINETYPE_NAME . '/' . LINE_ID;

    $menus[] = [[
        'href' => $base_href,
        'text' => LINETYPE_NAME . '/' . substr(LINE_ID, 0, 7),
        'current' => true,
    ]];
}

if ($childpath) {
    foreach ($childpath as $child) {
        $menus[] = [[
            'href' => $base_href .= '/' . $child->property,
            'text' => $child->property,
            'current' => true,
        ]];

        if ($child->id) {
            $display = $child->id ?? '??';

            if (preg_match('/^[0-9a-f]{64}$/', $display)) {
                $display = substr($display, 0, 7);
            }

            $menus[] = [[
                'href' => $base_href .= '/' . $child->id,
                'text' => $display,
                'current' => true,
            ]];
        }
    }
}

?><div class="sidebar" id="sidebar" data-area-margin="15" data-area-maxwidth="150"><?php
    foreach ($menus as $i => $menu) {
        ?><nav><?php
            if ($i === count($menus) - 1 || count($menu) === 1) { // last menu
                foreach ($menu as $item) {
                    ?><a<?php

                    ?> href="<?= $item['href'] ?>"<?php

                    if ($item['current']) {
                        ?> class="current"<?php
                    }

                    ?>><?php

                    echo $item['text']

                    ?></a><?php
                }
            } else {
                ?><select<?php
                ?> class="navigable current"<?php
                ?> data-url="<?= BASEPATH ?>/report/%"<?php
                ?>><?php

                foreach ($menu as $i => $item) {
                    ?><option<?php

                    ?> value="<?= $i ?>"<?php
                    ?> data-url="<?= $item['href'] ?>"<?php

                    if ($item['current']) {
                        ?> class="current" selected="selected"<?php
                    }

                    ?>><?= $item['text'] ?></option><?php
                }

                ?></select><?php
            }
        ?></nav><?php
    }
?></div><?php

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
                        ?> class="easy-table__cell list-value limitedwidth"<?php
                        ?> data-name="<?= $field->name ?>"<?php
                        ?> style="max-width: <?= bcdiv(90, count($fields), 2) ?>%; <?php if ($field->type == 'number'): ?>text-align: right;<?php endif ?>"<?php
                        ?>><div><?php

                            $display = $line->{$field->name} ?? '';

                            if ($field->name === 'id' || ($field->is_jars_id_reference ?? false)) {
                                $display = substr($display, 0, 7);
                            }

                            echo htmlspecialchars($display);

                        ?></div></div><?php
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
        ?><div style="text-align:center; margin-top: 2em"><?php
            ?><?= count($lines) ?> lines<?php
            ?>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<?php
            ?><a href="<?= BASEPATH ?>/raw/<?= REPORT_NAME ?>/<?= GROUP_NAME ?>">raw editor</a><?php

            if (CHILDPATH) {
                ?>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<?php
                $parentpath = $childpath;
                array_pop($parentpath);

                $parentpath_r = implode('/', array_map(fn ($item) => $item->property . '/' . $item->id, $parentpath));
                $suffix = $parentpath_r ? '/' . $parentpath_r : null;

                ?><a href="<?= BASEPATH ?>/report/<?= REPORT_NAME ?>/<?= GROUP_NAME ?>:<?= LINETYPE_NAME ?>/<?= LINE_ID . $suffix ?>">back</a><?php
            }

        ?></div><?php
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
                            ?> <a class="rawline" style="display: none">Raw</a><?php
                        ?></div><?php
                        ?><div style="clear: both"></div><?php
                        ?><br><?php
                    ?></div><?php

                    if ($children = array_filter($linetype->children, fn ($child) => isset($line->{$child->property}))) {
                        ?><div class="form-row"><?php
                            ?><div class="form-row__label">&nbsp;</div><?php
                            ?><div class="form-row__value"><?php
                                foreach ($children as $child) {
                                    ?><a class="childrenlink" data-property="<?= $child->property ?>"><?php

                                    echo $child->property;

                                    ?></a><br><?php
                                }
                            ?></div><?php
                            ?><div style="clear: both"></div><?php
                            ?><br><?php
                        ?></div><?php
                    }
                ?></form><?php
            ?></div><?php
        }
    ?></div><?php
}
