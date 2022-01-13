<?php

// init objects
$pagesModel = $Site->model('DB\Pages');
$pageTemplates = $Site->model('Frontend\Page\Templates');

// ajax process
if (!empty($_POST['action'])) {
    $cl = sprintf('Frontend\Admin\AJAX\Builder\%s', $_POST['action']);
    $AJAXResponse = $Site->model('Actions\AJAXActionHandler')->run($cl, $_POST);
    exit($AJAXResponse->getResponseJSON());
}

// page data
$pageID = !empty($_GET['ID']) ? (int) $_GET['ID'] : 0;
$pageData = $pageID > 0 ? $pagesModel->getByID($pageID) : [];

// page copy process
if (isset($_GET['createCopy']) && !empty($pageData)) {
    $pagesModel->save([
        'fields' => [
            'section' => $pageData['section'] ?? '',
            'template' => $pageData['template'] ?? '',
            'templateData' => $pageData['templateData'],
            'blocks' => $pageData['blocks'],
            'meta' => $pageData['meta']
        ]
    ]);
    $pageID = $pagesModel->getInsertID();
    header(sprintf('Location: /admin/builder/detail?ID=%d', $pageID));
    exit;
}

// template params
$templateParams = [];
if (!empty($pageData['template'])) {
    $currTemplate = $pageTemplates->getByName($pageData['template']);
    if (!empty($currTemplate)) {
        $templateParams = $currTemplate->meta()->params();
    }
}

$jquery = true;
$add_css[] = '/admin/module/builder/css/builder.css?t=' . time();
require_once 'template/tmpl_tiny_mce.php';
?>
<script type="text/javascript" src="/admin/module/builder/js/template.js?<?= time() ?>"></script>
<script type="text/javascript" src="/admin/module/builder/js/popupMaker.lib.js?<?= time() ?>"></script>
<script type="text/javascript" src="/admin/module/builder/js/builder.js?<?= time() ?>"></script>

<script>
window._app = {};

// данные страницы
window._app['pageData'] = {
    ID: <?= $pageID ?>,
    data: <?= json_encode($pageData, JSON_FORCE_OBJECT) ?>
};

// формы редактирования
window._app['pageForms'] = {};

// общие параметры
window._app['pageForms']['main'] = {
    selector: '#formEdit_main',
    params: [
        {
            title: 'Общие параметры',
            css: '',
            type: 'block-list',
            blockState: 'open',
            listRows: 4,
            value: [
                {
                    title: 'Активность',
                    type: 'select',
                    actionName: 'ListYesNo',
                    var: 'active'
                },
                {
                    title: 'Шаблон страницы',
                    type: 'templateSelect',
                    var: 'template',
                    actionName: 'ListTemplates'
                },
                {
                    title: 'Раздел',
                    type: 'select',
                    var: 'section',
                    actionName: 'ListSections'
                },
                {
                    title: 'Адрес страницы',
                    type: 'string',
                    var: 'url'
                },
                {
                    title: 'SEO Заголовок',
                    type: 'string',
                    var: 'meta[seo_title]'
                },
                {
                    title: 'SEO Описание',
                    type: 'text',
                    var: 'meta[seo_description]'
                }
            ]
        }
    ]
};

// параметры шаблона
window._app['pageForms']['template'] = {
    selector: '#formEdit_template',
    params: <?= json_encode($templateParams) ?>
};

// управление блоками
window._app['pageForms']['blocks'] = {
    selector: '#formEdit_blocks',
    params: [
        {
            title: 'Блоки',
            type: 'block-list',
            value: [
                {
                    title: 'Блок',
                    type: 'multiple',
                    var: 'blocks',
                    value: [
                        {
                            type: 'block-list',
                            css: '',
                            listRows: 5,
                            value: [
                                {
                                    title: 'Тип блока',
                                    type: 'blockSelect',
                                    var: 'name',
                                    actionName: 'ListBlocks'
                                },
                                {
                                    title: 'Показывать',
                                    type: 'select',
                                    var: 'active',
                                    actionName: 'ListYesNo'
                                },
                                {
                                    title: 'Версия шаблона',
                                    type: 'blockVersions',
                                    var: 'version'
                                },
                                {
                                    title: 'А/Б тест',
                                    type: 'select',
                                    var: 'abtest',
                                    actionName: 'ListABTest'
                                },
                                {
                                    title: 'Параметры',
                                    type: 'blockEdit',
                                    var: 'params'
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
};
</script>

<?php
if (!empty($pageData)) {
    ?>
    <style type="text/css">
    div.multiple[data-field="blocks"] div.multipleItemPanel a[data-click="blockCopyTo"] {
        display: initial;
    }
    </style>
    <?php
}
?>

<div class="adminOuter">
    <div style="height: 20px;"></div>
    <div><a href="/admin/builder/list"><button>Список страниц</button></a></div>
    <div style="height: 20px;"></div>
    <h3><?= !empty($pageData) ? 'Редактирование' : 'Создание' ?> страницы</h3>
    <div style="display: grid;
    grid-template-columns: auto auto;
    column-gap: 10px;
    background: rgba(220, 220, 220, 0.95);
    margin-top: 20px;
    height: 40px;
    align-items: center;">
        <div style="display: grid;
        grid-template-columns: repeat(3, auto);
        column-gap: 10px;
        justify-content: left;
        align-items: center;
        padding-left: 10px;">
            <?php
            if (!empty($pageData)) {
                ?>
                <div style="background: rgb(239, 239, 239);padding: 4px;"><b>ID:</b> <?= $pageID ?></div>
                <div style="background: rgb(239, 239, 239);padding: 4px;"><b>Дата создания:</b> <?= $pageData['dateCreate'] ?></div>
                <div style="background: rgb(239, 239, 239);padding: 4px;"><b>Дата изменения:</b> <?= $pageData['dateModify'] ?></div>
                <?php
            }
            ?>
        </div>
        <div style="display: grid;
        grid-template-columns: repeat(4, auto);
        column-gap: 10px;
        justify-content: right;">
            <div><a href="/admin/builder/detail"><button>Создать новую</button></a></div>
            <?php
            if (!empty($pageID)) {
                $url = $_SERVER['REQUEST_URI'] . '&createCopy';
                ?>
                <div><a href="<?= $url ?>"><button>Создать копию</button></a></div>
                <?php
            }
            ?>
            <?php
            if (!empty($pageData['url'])) {
                $url = !empty($pageData['active'])
                    ? $pageData['urlFull']
                    : $pageData['urlWithHash'];
                ?>
                <div><a href="<?= $url ?>" target="_blank"><button>Открыть страницу</button></a></div>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="content" id="formEdit">
        <div id="formEdit_main"></div>
        <div id="formEdit_template" style="margin-top: 15px;"></div>
        <div id="formEdit_blocks"></div>
        <button class="btnSend disabled" data-click="send">Сохранить</button>
    </div>
</div>