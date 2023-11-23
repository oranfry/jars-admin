<input<?php

?> autocomplete="off"<?php
?> class="field value"<?php
?> name="<?= $name ?>"<?php

if ($dp) {
    ?> step="<?= 1 / pow(10, $dp) ?>"<?php
}

?> style="width: 8em"<?php
?> type="number"<?php

?>><?php
