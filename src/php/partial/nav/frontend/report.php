<?php
    $query = implode('&', array_map(function($v, $k) { return "{$k}={$v}"; }, $_GET, array_keys($_GET)));
    $query = $query ? '?' . $query : '';
?>

<div class="navset">
    <div class="nav-title">Report</div>
    <div class="inline-rel">
        <div class="inline-modal listable">
            <div class="inline-dropdown">
                <?php foreach ($reports as $report) : ?>
                    <a href="/report/<?= $report->name ?><?= $query ?>" <?= REPORT_NAME == $report->name ? 'class="current"' : '' ?>><?= $report->name ?></a>
                <?php endforeach ?>
            </div>
        </div>
        <span class="inline-modal-trigger"><?= REPORT_NAME ?></span>
    </div>
</div>

<div class="navset">
    <div class="nav-title">Group</div>
    <div class="inline-rel">
        <div class="drnav">
            <a
                class="icon icon--gray icon--arrowleft <?= $prevGroup ? 'cv-manip' : 'disabled' ?>"
                data-manips="<?= REPORT_NAME ?>_group__value=<?= $prevGroup ?>"
                <?= $prevGroup ? null : 'style="visibility: hidden"' ?>
            ></a>
        </div>
        <select class="cv-surrogate" data-for="<?= REPORT_NAME ?>_group__value">
            <?php foreach ($groups as $_group) : ?>
                <option<?= $_group == $group ? ' selected="selected"' : null ?>><?= $_group ?></option>
            <?php endforeach ?>
        </select>
        <div class="drnav">
            <a
                class="icon icon--gray icon--arrowright <?= $nextGroup ? 'cv-manip' : 'disabled' ?>"
                data-manips="<?= REPORT_NAME ?>_group__value=<?= $nextGroup ?>"
                <?= $nextGroup ? null : 'style="visibility: hidden"' ?>
            ></a>
        </div>
    </div>
</div>
