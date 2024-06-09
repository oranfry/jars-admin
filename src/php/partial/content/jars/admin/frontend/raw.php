<script>let base_version = '<?= $base_version ?>';</script><?php

?><div style="margin: 1em"><?php
    ?><a href="<?= $back ?>">Back</a><br><br><?php

    ?><form method="post"><?php
        ?><div><?php
            ?><textarea name="raw" class="raw"><?= json_encode($line, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></textarea><?php
            ?><br><?php
            ?><br><?php
            ?><button class="savelineraw button button--main" type="button">Save</button><?php
        ?></div><?php
    ?></form><?php
?></div><?php
?><script>let back = '<?= $back ?>';</script><?php