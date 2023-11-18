<?php

(function() use ($name, $field) {
    $step = 1 / pow(10, $field->dp ?? 0);

    ?><input<?php

    ?> autocomplete="off"<?php
    ?> class="field value"<?php
    ?> name="<?= $name ?>"<?php
    ?> step="<?= $step ?>"<?php
    ?> style="width: 8em"<?php
    ?> type="number"<?php

    ?>><?php
})();
