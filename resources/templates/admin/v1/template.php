<?php
$rootDir = $app->data()->get('rootDir') ?? '/';
$fileCacheFlag = time();
$pageContent = $app->data()->get('pageContent') ?? '';
?><!DOCTYPE html>
<html>
<head>
    <title>Панель управления - <?= $app->data()->get('title') ?? '' ?></title>
    <link href="<?= $rootDir ?>resources/css/admin/styles.css?v<?= $fileCacheFlag ?>" type="text/css" rel="stylesheet">
    <link href="<?= $rootDir ?>resources/css/admin/builder.css?v<?= $fileCacheFlag ?>" type="text/css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!--link href="<?= $rootDir ?>node_modules/@fancyapps/ui/dist/fancybox.css?v<?= $fileCacheFlag ?>" type="text/css" rel="stylesheet"-->
    <!--script type="text/javascript" src="<?= $rootDir ?>node_modules/@fancyapps/ui/dist/fancybox.umd.js?v<?= $fileCacheFlag ?>"></script-->
    <link href="<?= $rootDir ?>resources/js/admin/builder/jquery.fancybox.min.css?v<?= $fileCacheFlag ?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?= $rootDir ?>resources/js/admin/builder/jquery.fancybox.min.js?v<?= $fileCacheFlag ?>"></script>
    <script type="text/javascript" src="<?= $rootDir ?>resources/js/public/handlers.js?v<?= $fileCacheFlag ?>"></script>

    <script type="text/javascript" src="<?= $rootDir ?>resources/js/admin/admin.lib.js?v<?= $fileCacheFlag ?>"></script>
    <script type="text/javascript" src="<?= $rootDir ?>resources/js/admin/build.admin.js?v<?= $fileCacheFlag ?>"></script>
</head>
<body>
<script>
window._kadmin = {};
window._kadmin['rootDir'] = "<?= $rootDir ?>";
</script>
    <div class="header">
        <div class="logo"><a href="<!-- v[rootDir] -->">&#9000; Панель управления</a></div>
    </div>
    
    <div class="outer">
        <div class="leftContent">
            <div class="title">Меню</div>
            <div id="leftMenu"></div>
        </div>
        
        <div class="content">
            <?= $pageContent ?>
        </div>
    </div>

    <div style="height: 40rem;"></div>
    
    <div class="footer">
        <div class="boxInfo">&copy; <a href="#">pavapp</a> <?= date('Y') ?></div>
    </div>
</body>
</html>