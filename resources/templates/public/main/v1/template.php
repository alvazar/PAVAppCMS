<?php
$rootDir = $app->data()->get('rootDir') ?? '/';
$fileCacheFlag = time();
$pageContent = $content;

$topMenu = $app->data()->get('topMenu') ?? [];
?><!DOCTYPE html>
<html>
<head>
    <title>My Site - <?= $app->data()->get('title') ?? '' ?></title>
    <link href="<?= $rootDir ?>resources/css/public/styles.css?v<?= $fileCacheFlag ?>" type="text/css" rel="stylesheet">
    <link href="<?= $rootDir ?>resources/css/public/blocks.css?v<?= $fileCacheFlag ?>" type="text/css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="<?= $rootDir ?>node_modules/@fancyapps/ui/dist/fancybox.css?v<?= $fileCacheFlag ?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?= $rootDir ?>node_modules/@fancyapps/ui/dist/fancybox.umd.js?v<?= $fileCacheFlag ?>"></script>
    <script type="text/javascript" src="<?= $rootDir ?>resources/js/public/handlers.js?v<?= $fileCacheFlag ?>"></script>
</head>
<body>
    
    <header>
        <div class="logo"><a href="<?= $rootDir ?>">&#9000; My Site</a></div>
    </header>
    
    <?php
    if (!empty($topMenu)) {
        ?>
        <section class="topMenu">
            <div class="menuItems">
                <?php
                foreach ($topMenu as $item) {
                    ?>
                    <div><a href="<?= $item['value'] ?>"><?= $item['name'] ?></a></div>
                    <?php
                }
                ?>
            </div>
        </section>
        <?php
    }
    ?>
    
    <section class="pageContent">
        <div class="content">
            <?= $pageContent ?>
        </div>
    </section>
    
    <footer>
        <div>&copy; <a href="#">My Site</a> <?= date('Y') ?></div>
    </footer>
</body>
</html>