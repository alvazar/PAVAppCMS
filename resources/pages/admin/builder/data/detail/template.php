<?php
// init objects
$dataBlocksModel = $Site->model('DB\DataBlocks');
$dataBlocks = $Site->model('Frontend\Page\DataBlocks');
//$dataBlocksModel->install(true);

// ajax process
if (!empty($_POST['action'])) {
    $_POST['action'] = str_ireplace('SaveData', 'SaveBlockData', $_POST['action']);
    $cl = sprintf('Frontend\Admin\AJAX\Builder\%s', $_POST['action']);
    $AJAXResponse = $Site->model('Actions\AJAXActionHandler')->run($cl, $_POST);
    exit($AJAXResponse->getResponseJSON());
}

// page data
$dataBlockID = !empty($_GET['ID']) ? (int) $_GET['ID'] : 0;
$dataBlockData = $dataBlockID > 0 ? $dataBlocksModel->getByID($dataBlockID) : [];

// dataBlock params
$dataBlockParams = [];
if (!empty($dataBlockData['type'])) {
    $currDataBlock = $dataBlocks->getByName($dataBlockData['type']);
    if (!empty($currDataBlock)) {
        $dataBlockParams = $currDataBlock->meta()->params();
    }
}
?>
<script type="text/javascript" src="/resources/js/admin/builder/template.js?<?= time() ?>"></script>
<script type="text/javascript" src="/resources/js/admin/builder/popupMaker.lib.js?<?= time() ?>"></script>
<script type="text/javascript" src="/resources/js/admin/builder/builder.js?<?= time() ?>"></script>

<script>
window._app = {};

// данные страницы
window._app['pageData'] = {
    ID: <?= $dataBlockID ?>,
    data: <?= json_encode($dataBlockData, JSON_FORCE_OBJECT) ?>
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
                    title: 'Ключ',
                    type: 'string',
                    var: 'dataKey'
                },
                {
                    title: 'Название',
                    type: 'string',
                    var: 'name'
                },
                {
                    title: 'Тип данных',
                    type: 'dataBlockSelect',
                    var: 'type',
                    actionName: 'ListDataBlocks'
                },
            ]
        }
    ]
};

// параметры данных
window._app['pageForms']['dataBlock'] = {
    selector: '#formEdit_dataBlock',
    params: <?= json_encode($dataBlockParams) ?>
};
</script>

<div class="adminOuter">
    <div style="height: 20px;"></div>
    <div><a href="/admin/builder/data/list"><button>Список</button></a></div>
    <div style="height: 20px;"></div>
    <h3><?= !empty($dataBlockData) ? 'Редактирование' : 'Создание' ?></h3>
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
            if (!empty($dataBlockData)) {
                ?>
                <div style="background: rgb(239, 239, 239);padding: 4px;"><b>ID:</b> <?= $dataBlockID ?></div>
                <div style="background: rgb(239, 239, 239);padding: 4px;"><b>Дата создания:</b> <?= $dataBlockData['dateCreate'] ?></div>
                <div style="background: rgb(239, 239, 239);padding: 4px;"><b>Дата изменения:</b> <?= $dataBlockData['dateModify'] ?></div>
                <?php
            }
            ?>
        </div>
        <div style="display: grid;
        grid-template-columns: repeat(4, auto);
        column-gap: 10px;
        justify-content: right;">
            <div><a href="/admin/builder/data/detail"><button>Создать новую</button></a></div>
            <?php
            if (!empty($dataBlockID)) {
                $url = $_SERVER['REQUEST_URI'] . '&createCopy';
                ?>
                <div><a href="<?= $url ?>"><button>Создать копию</button></a></div>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="content" id="formEdit">
        <div id="formEdit_main"></div>
        <div id="formEdit_dataBlock" style="margin-top: 15px;"></div>
        <button class="btnSend disabled" data-click="send">Сохранить</button>
    </div>
</div>