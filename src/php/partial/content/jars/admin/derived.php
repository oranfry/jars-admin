<?php

if (isset($base_version)) {
    echo '<script>';
    ?>let base_version = '<?= $base_version ?>';<?php
    echo '</script>';
}
if (!$report) {
    ?><h3>No Derived Reports</h3><?php
}

?><div style="margin: 1em 0"><?php
    ?><form method="post"><?php
        ?><div><?php
            ?><textarea name="raw" class="raw"><?= json_encode($data ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></textarea><?php
            ?><br><?php
            ?><br><?php
            ?><button class="savelineraw button button--main" type="button">Save</button><?php
        ?></div><?php
    ?></form><?php
?></div><?php
