<?php
ini_set('display_errors', true);

$pagesModel = $Site->model('DB\Pages');
//$pagesModel->install();

// ajax process
if (!empty($_POST['action'])) {
    $response = [
        'type' => 'error',
        'message' => ''
    ];

    $action = $_POST['action'] ?? '';
    $section = $_POST['section'] ?? '';
    $numPage = (int) ($_POST['numPage'] ?? '');
    unset($_POST['action']);
    if ($action === 'getList') {
        $whereParams = [];
        if (!empty($section)) {
            $whereParams['section'] = $section;
        }
        $response['type'] = 'success';

        $response['items'] = $pagesModel->getList([
            'where' => $whereParams,
            'orderBy' => [
                'active' => 'desc',
                'sort' => 'desc',
                'ID' => 'desc'
            ]
        ]);
    }
    exit(json_encode($response));
}

$sections = ['' => 'Все страницы'];
$sections = array_merge($sections, $Site->model('Frontend\Admin\AJAX\Builder\ListSections')->run());
?>
<style type="text/css">
.main {
    display: grid;
    grid-template-columns: 220px 1fr;
    align-items: baseline;
    column-gap: 20px;
    margin-top: 5px;
}
.sections {
    display: grid;
    row-gap: 4px;
}
.sections > div {
    display: grid;
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #5e676d;
}
.sections > div:hover {
    background-color: #5e676d;
}
.sections > div:hover a {
    color: white;
}
.sections > div a {
    color: #5e676d;
    text-decoration: none;
}
</style>
<div class="adminOuter">
    <div style="height: 20px;"></div>
    <div>
        <a href="/admin/builder/detail"><button>Создать страницу</button></a>
    </div>
    <div style="height: 20px;"></div>
    <h3>Список страниц</h3>
    <div style="height: 20px;"></div>
    <div class="main">
        <div class="sections">
            <?php
            foreach ($sections as $section => $name) {
                ?>
                <div><a 
                        href="/admin/builder/list?section=<?= $section ?>" 
                        data-click="showList" 
                        data-section="<?= $section ?>"><?= $name ?></a></div>
                <?php
            }
            ?>
        </div>
        <div class="actionList" id="list_items">
            <div style="grid-template-columns: 40px 63px 130px 300px 280px 50px;">
                <div>ID</div>
                <div>Активно</div>
                <div>Дата изменения</div>
                <div>Адрес</div>
                <div>Доп. инфо</div>
                <div></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
const templates = {
    item: `<div style="grid-template-columns: 40px 63px 130px 300px 280px 50px;">
        <div>{ID}</div>
        <div>{is_active}</div>
        <div>{dateModify}</div>
        <div><a href="{urlWithHash}" target="_blank">{urlFull}</a></div>
        <div>{info}</div>
        <div><a href="/admin/builder/detail?ID={ID}" title="Редактировать страницу"><svg class="adminIcon">
                    <use href="/public/images/feather-sprite.svg#edit"/>
                </svg></a></div>
    </div>`
};

class Pages
{
    constructor()
    {
        this.items = {};
    }

    showList(sendData)
    {
        let listPlace = $('#list_items');
        listPlace.children('div:not(:first)').remove();

        sendData = Object.assign({action: 'getList'}, sendData);
        console.log('send data', sendData);
        FormHandler.send('', sendData).then(data => {
            let listHTML = '';
            for (let i in data['items']) {
                let item = data['items'][i];
                item['is_active'] = 'Нет';
                if (parseInt(item['active']) == 1) {
                    item['is_active'] = 'Да';
                    item['urlWithHash'] = item['urlFull'];
                }
                this.items['w_' + item['ID']] = item;
                listHTML += TextHandler.replaceInText(templates['item'], item);
            }
            if (listHTML == '') {
                listHTML = '<div>Элементы не найдены</div>';
            }
            listPlace.append(listHTML);
            eventsHandler.init(listPlace);
        }).catch(error => {
            listPlace.append('<div>Произошла ошибка</div>');
            console.log(error);
        });
    }

    /*
    save(formObj)
    {
        let formData = new FormData(formObj[0]);
        formData.append("action", "save");
        return FormHandler.send('', formData);
    }
    */

    getData(ID)
    {
        const keyName = 'w_' + ID;
        return keyName in this.items ? this.items[keyName] : {};
    }
}
let itemsHandler = new Pages();
let eventsHandler = new EventsHandler();

eventsHandler.add('popupClose', e => {
    e.preventDefault();
    $('.popup').hide();
});

// open save form
eventsHandler.add('openEdit', e => {
    let curr = $(e.currentTarget);
    let formSend = $('#form_save');
    const ID = curr.attr('data-ID');
    let itemData = itemsHandler.getData(ID);
    formInsertData(formSend, itemData);
    formSend.hide().slideToggle();
    // scroll to edit form
    PageHandler.scroll('#form_save', -50);
});

// open add form
eventsHandler.add('add', e => {
    let curr = $(e.currentTarget);
    let formSend = $('#form_save');
    formClearData(formSend);
    formSend.hide().slideToggle();
});

// close form
eventsHandler.add('close', e => {
    let curr = $(e.currentTarget);
    let formSend = $('#form_save');
    formClearData(formSend);
    formSend.slideToggle();
});

eventsHandler.add('showList', e => {
    let curr = $(e.currentTarget);
    let section = curr.attr('data-section');
    let numPage = curr.attr('data-numPage');
    let sendData = {};
    if (typeof section == 'string') {
        sendData['section'] = section;
    }
    if (typeof numPage == 'string') {
        sendData['numPage'] = numPage;
    }
    itemsHandler.showList(sendData);
});

//
$(() => {
    // first load
    itemsHandler.showList();
    eventsHandler.init();
});
</script>