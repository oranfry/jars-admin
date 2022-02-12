<!DOCTYPE html>
<html lang="en-NZ">
<head>
    <meta name="viewport" content="width=320, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/build/css/styles.<?= latest('css') ?>.css">
    <meta charset="utf-8"/>
    <title>Jars Admin &bull; <?= $title ?? PAGE ?></title>
</head>
<body class="centered">
    <?php require search_plugins('src/php/partial/content/' . (defined('VIEW') ? VIEW : PAGE) . '.php'); ?>

    <?php if ($jars->token()): ?>
        <?php $username = $jars->token_username($jars->token()); ?>
        <?php $user = $jars->token_user($jars->token()); ?>

        <script>
            window.orig_token = '<?= $jars->token() ?>';

            <?php foreach (PAGE_PARAMS as $key => $value): ?>
                window.<?= "{$key} = '{$value}'"; ?>;
            <?php endforeach ?>
        </script>
    <?php endif ?>

    <script type="text/javascript" src="/build/js/app.<?= latest('js') ?>.js"></script>
    <?php @include APP_HOME . '/src/php/partial/js/' . PAGE . '.php'; ?>

</body>
</html>