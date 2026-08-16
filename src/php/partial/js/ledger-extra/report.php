<?php

use OranFry\ContextVariableSets\ContextVariableSet;

$lastChildpath = end(ContextVariableSet::get('childpath')->value);

echo '<script>';

if (LINE_ID) {
    ?>window.line_id = '<?= LINE_ID ?>';<?php
    ?>window.linetype_name = '<?= LINETYPE_NAME ?>';<?php

    if (!$lastChildpath) {
        ?>$('.linerow[data-type="<?= LINETYPE_NAME ?>"][data-id="<?= LINE_ID ?>"]').click();<?php
    } elseif ($lastChildpath->id) {
        ?>$('.linerow[data-id="<?= $lastChildpath->id ?>"]').click();<?php
    }
}

?>window.reportLinetypes = <?= json_encode($ledger->linetypeDetails()) ?>;<?php
?>window.context = <?= json_encode($ledger->context()) ?>;<?php

?>window.customRefreshUrl = adminMakeUrl;<?php
?>window.postRefreshLineEditor = (window.postRefreshLineEditor ?? []).concat([refreshLineCvs]);<?php
?>window.ledgerUnmapLine = (window.ledgerUnmapLine ?? []).concat([adminUnmapLine]);<?php
?>window.ledgerPostSave = (window.ledgerPostSave ?? []).concat([adminPostSave]);<?php

?>refreshLineCvs();<?php

echo '</script>';
