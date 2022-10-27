// admin

function getObjectHash(obj) {
    let objStr = JSON.stringify(obj);
    let hash = 0;
    for (let i = 0; i < objStr.length; i++) {
        let character = objStr.charCodeAt(i);
        hash = ((hash << 5) - hash) + character;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
}

function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function getObjectPropsCount(obj, countRecursive) {
    let cnt = 0;
    for (let i in obj) {
        if (countRecursive === true && typeof obj[i] == 'object') {
            cnt += getObjectPropsCount(obj[i], countRecursive);
        } else {
            cnt++;
        }
    }
    return cnt;
}

function tooltip() {
    let xOffset = 0;
    let yOffset = 20;
    window._app['builderTooltipFix'] = false;

    let targets = $('[data-tooltip]');

    targets.unbind('hover').hover(e => {
        tooltipClose();
        let curr = $(e.currentTarget);
        let ttl = escapeHtml(curr.attr('data-tooltip'));
        ttl = ttl.replace(/\n/g, '<br />');
        $("body").append(`<div id="tooltip"><div class="close"><svg class="adminBuilderEditIcon" onclick="tooltipClose();">
        <use href="/admin/module/builder/images/feather-sprite.svg#x-circle" /></div>${ttl}</div>`);
        $("#tooltip")
            .css("top",(e.pageY - xOffset) + "px")
            .css("left",(e.pageX + yOffset) + "px")
            .delay(800)
            .fadeIn(200);
     },
     () => {
        if (window._app['builderTooltipFix'] === false) {
            $("#tooltip").remove();
        }
     });
     targets.mousemove(e => {
        if (window._app['builderTooltipFix'] === false) {
            $("#tooltip")
                .css("top",(e.pageY - xOffset) + "px")
                .css("left",(e.pageX + yOffset) + "px");
        }
    });
}

function tooltipClose() {
    window._app['builderTooltipFix'] = false;
    $("#tooltip").remove();
}

// constructor
class KAdmin
{
    constructor()
    {
    }

    // methods
    getData(action)
    {
        return new Promise((resolve, reject) => {
            $.ajax("", {
                data: {action: action},
                type: "post",
                dataType: "JSON",
                success: result => {
                    if (result['type'] == "success") {
                        resolve(result);
                    }
                    else {
                        reject();
                    }
                },
                error: (a, b) => console.log('error getData', b)
            });
        });
    }

    sendData(data)
    {
        let sendData = Object.assign(
            {
                action: "SaveData"
            },
            data
        );
        console.log('send data', sendData);
        return new Promise((resolve, reject) => {
            $.ajax("", {
                data: sendData,
                type: "post",
                dataType: "JSON",
                success: result => result['type'] == "success" ? resolve(result) : reject(result['result']),
                error: () => {
                    $.ajax("", {
                        data: sendData,
                        type: "post",
                        dataType: "HTML",
                        success: result => reject(result)
                    });
                }
            });
        });
    }
}

// admin form
class KAdminForm
{
    constructor(params)
    {
        this.data = 'data' in params ? params['data'] : {};
        this.actionsResult = new Map();
        console.log('init form', this.data);
        this.popup = new PopupMaker();
        this.templ = new Template();
        this.templates = 'templates' in params ? params['templates'] : {};
        this.adminPage = 'adminPage' in params ? params['adminPage'] : null;
        this.ID = 'ID' in params ? params['ID'] : 0;
    }

    // methods
    convertToObject(key, value)
    {
        //
        value = value !== undefined ? value : "";
        if (typeof value == 'string') {
            value = JSON.stringify(value);
            /*
            value = value.replace(/\"/g, '\\"')
                         .replace(/\\/g, '\\\\');
            value = '"'+value+'"';
            */
        } else if (typeof value == 'object') {
            value = JSON.stringify(value);
        }
        //
        let obj = key;
        // set value as list item
        if (obj.match(/\[\]$/) !== null) {
            obj = obj.replace("[]", "");
            value = '['+value+']';
        }
        //
        obj = obj.replace(/\]\[/g, ":{").
                  replace(/\[/g, ":{").
                  replace(/\]/g, "");
        let levels = obj.match(/\{/g);
        levels = levels === null ? 0 : levels.length;
        obj += "}".repeat(levels);
        obj = "{" + obj + "}";
        obj = obj.replace(/([a-z0-9_-]+)/ig, '"$1"').
                  replace(/\"\}/, '":' + value + '}').
                  replace(/\n/g, '\\n').
                  replace(/\t/g, '\\t');
        
        return JSON.parse(obj);
    }
    
    set(key, value)
    {
        const obj = this.convertToObject(key, value);
        const updateData = (cb, data, obj) => {
            for (let key in obj) {
                if (key in data) {
                    if (obj[key] instanceof Array) {
                        data[key] = data[key] instanceof Array ? 
                                data[key].concat(obj[key]) : obj[key];
                    } else if (typeof obj[key] == "object" && 
                            typeof data[key] == "object") {
                        cb(cb, data[key], obj[key]);
                    } else {
                        data[key] = obj[key];
                    }
                }
                else {
                    data[key] = obj[key];
                }
            }
        }
        this.unset(key);
        updateData(updateData, this.data, obj);
        //console.log(JSON.stringify(this.data));
        console.log('after set data', key, this.data);
    }

    get(key)
    {
        if (typeof key === 'undefined') {
            return this.data;
        }
        const obj = this.convertToObject(key);
        const getData = (cb, data, obj) => {
            for (let key in obj) {
                if (typeof data !== "object" || !(key in data)) {
                    return "";
                }
                else if (typeof obj[key] === "object") {
                    return cb(cb, data[key], obj[key]);
                }
                else {
                    return data[key];
                }
            }
        }
        return getData(getData, this.data, obj);
    }

    unset(key)
    {
        const unsetData = (cb, data, obj) => {
            for (let key in obj) {
                if (key in data) {
                    if (obj[key] == "_endPoint_") {
                        if (data instanceof Array) {
                            data.splice(key, 1);
                        }
                        else {
                            delete data[key];
                        }
                    }
                    else if (typeof obj[key] == "object" && 
                            typeof data[key] == "object") {
                        cb(cb, data[key], obj[key]);
                    }
                }
            }
        }
        const obj = this.convertToObject(key, "_endPoint_");
        unsetData(unsetData, this.data, obj);
        console.log('after delete', key, this.data);
    }

    send()
    {
        this.popup.show({
            title: "Отправка...",
            contents: "Идёт сохранение данных"
        });
        
        const admin = new KAdmin();
        admin.sendData({
            data: this.data,
            ID: this.ID
        }).then(response => {
            console.log('new id', response['newID']);
            if ('data' in response && parseInt(response['data']['newID']) > 0) {
                this.ID = response['data']['newID'];
                history.pushState(null, document.title, `?ID=${this.ID}`);
            }
            this.popup.show({
                title: "Данные успешно сохранены",
                buttons: {
                    confirm: {
                        title: "Обновить страницу",
                        click: () => window.location.reload()
                    }
                }
            });
        }).catch(error => {
            this.popup.show({
                title: "Упс...",
                contents: "Ошибка! - " + error
            });
        });
    }

    async make(fields, prepareItem, prepareField)
    {
        // make HTML form for edit data
        
        /*
        * title: <string> Название поля или списка полей.
        * type: <string> Тип поля или тип списка. Значения: block-list, multiple, string, text, integer, select, date, image.
        * var: <string> Имя переменной для хранения в базе.
        * value: <string|array> Значение поля или список полей.
        * blockState: <string> Состояние блока. Значения: open, close.
        * listRows: <integer> Кол-во рядов в списке.
        * css: <string> Название стиля css.
        * actionName: <string> Название запроса по AJAX.
        * actionParams: <object> Параметры запроса по AJAX.
        */
        
        // init
        let form = "";
        let step = 0;
        
        // make fields
        for (let i in fields) {
            let item = fields[i];
            let fieldHTML = '';
            
            // FIX! need for save first state var
            item['key'] = 'var' in item ? item['var'] : "";
            
            //
            if (prepareItem !== undefined) {
                item = prepareItem(item);
            }
            step += 1;
            item['step'] = step;
            let templParams = {
                item: item,
                value: this.get(item['key']),
                prepareItem: prepareItem
            };
            switch (item['type'] !== undefined ? item['type'] : "") {
                // block list
                case "block-list":
                    //fieldHTML = this.templ['fields'].get('blockList',templParams);
                    fieldHTML = await this.makeBlockList(templParams);
                    break;
                // multiple
                case "multiple":
                    //fieldHTML = this.templ['fields'].get('multiple',templParams);
                    fieldHTML = await this.makeMultiple(templParams);
                    break;
                case "blockEdit":
                    let blockEditValue = '';
                    /*
                    if (typeof templParams['value'] == 'object') {
                        for (let fieldName in templParams['value']) {
                            let valueType = typeof templParams['value'][fieldName];
                            let val = ''
                            if (valueType == 'object') {
                                val = 'object';
                            } else if (valueType == 'number') {
                                val = templParams['value'][fieldName];
                            } else if (valueType == 'string') {
                                val = templParams['value'][fieldName].length > 10
                                    ? templParams['value'][fieldName].substr(0, 10) + '...'
                                    : templParams['value'][fieldName];
                                val = escapeHtml(val);
                            }

                            blockEditValue += `<div style="font-size: 12px"><b>${fieldName}:</b> ${val}</div>`;
                        }
                    }
                    */
                    blockEditValue = '<div style="font-size: 12px"><b>Заполнено параметров:</b> ' + 
                        getObjectPropsCount(templParams['value'], true) + '</div>';
                    templParams['value'] = blockEditValue;
                    fieldHTML = this.makeField(templParams);
                    break;
                // single field
                default:
                    //
                    if (item['actionName'] !== undefined) {
                        let actionResult = await this.sendAction(item['actionName'], item['actionParams']);
                        if (actionResult[templParams['value']] !== undefined) {
                            templParams['value'] = actionResult[templParams['value']];
                        }
                        fieldHTML = this.makeField(templParams);
                        
                    } else {
                        templParams['value'] = escapeHtml(templParams['value']);
                        fieldHTML = this.makeField(templParams);
                    }
                    
                    break;
            }
            
            //
            form += prepareField !== undefined ? prepareField(fieldHTML) : fieldHTML;
        }
        
        //
        return form;
    }

    sendAction(actionName, actionParams)
    {
        let sendData = {
            action: actionName,
            pageID: 'ID' in window._app['pageData'] 
                ? window._app['pageData']['ID'] 
                : 0
        };
        if (typeof actionParams == 'object') {
            sendData = Object.assign(sendData, actionParams);
        }

        let hashCode = getObjectHash(sendData);

        return new Promise((resolve, reject) => {
            if (this.actionsResult.has(hashCode)) {
                console.log('action send get from cache', actionName, hashCode);
                resolve(this.actionsResult.get(hashCode));
            } else {
                FormHandler.send('', sendData).then(response => {
                    this.actionsResult.set(hashCode, response['data']);
                    console.log('action send new', actionName, hashCode);
                    resolve(response['data']);
                }).catch(error => console.log('send action error', actionName, error));
            }
        });
    }

    makeField(params)
    {
        //
        let item = params['item'];
        let key = item['key'];
        let value = params['value'];
        if (value != "") {
            if (item['type'] == 'image') {
                value = '<img src="' + value + '" alt="">';
            } else if (item['type'] == 'video') {
                value = `<video poster="" id="" width="100" height="auto" autoplay="autoplay" loop="loop" preload="auto" muted="false">
                    <source src="${value}">
                </video>`;
            }
        }

        let templateData = {
            templ: {
                key: key,
                title: item['title'],
                type: item['type'],
                value: value,
                actionName: item['actionName'] !== undefined ? item['actionName'] : '',
                actionParams: 'actionParams' in item
                    ? encodeURIComponent(JSON.stringify(item['actionParams']))
                    : '',
                aboutField: 'aboutField' in item 
                    ? {aboutFieldContent: escapeHtml(item['aboutField'])}
                    : null
            }
        };
        return this.templ.makeTemplate(this.templates['field'], templateData);
    }

    async makeBlockList(params)
    {
        let item = params['item'];
        
        item['blockState'] = item['blockState'] !== undefined ? item['blockState'] : "open";
        item['listRows'] = item['listRows'] !== undefined ? parseInt(item['listRows']) : 1;
        
        let templateData = {
            templ: {
                css: '',
                blockTitle: null,
                blockList: {}
            }
        };
        
        const css = 'css' in item ? item['css'] : ' blue';
        templateData['templ']['css'] = css;
        if ('title' in item) {
            let blockTitle = item['title'];
            // title from var
            const matchVar = /(\w+\:|)(\_\_item\S+)/g;
            let varData;
            while (varData = matchVar.exec(item['title'])) {
                const varName = varData[2].replace("__item", item['key']);
                let varValue = this.get(varName);
                if (varData[1].length > 0) {
                    varValue = await this.sendAction(varData[1].replace(':', ''))[varValue];
                }
                blockTitle = blockTitle.replace(varData[0], varValue);
            }
            templateData['templ']['blockTitle'] = {
                blockState: item['blockState'],
                blockTitle: blockTitle,
                pm: item['blockState'] == "open" ? '&#9650;' : '&#9660;'
            };
        }
        let cssStyle = 'grid-template-columns: repeat(' + item['listRows'] + ',1fr);';
        cssStyle += (item['blockState'] == "close" ? 'display: none;' : '');
        let fields = "";
        const parentKey = item['key'];
        fields += await this.make(item['value'], item => {
            if (parentKey.length > 0) {
                item['key'] = 'var' in item
                    ? parentKey + item['var'].replace(/^([^\[]+)/, '[$1]')
                    : parentKey;
            }
            return item;
        }, fieldHTML => {
            let result = '';
            result += '<div class="cell' + css + '">' + fieldHTML + '</div>';
            return result;
        });

        templateData['templ']['blockList'] = {
            cssStyle: cssStyle,
            fields: fields
        };
        
        return this.templ.makeTemplate(templates['blockList'], templateData);
    }

    async makeMultiple(params)
    {
        let item = params['item'];
        
        const fnGetCount = obj => {
            let cnt = 0;
            for (let i in obj) {
                cnt++;
            }
            return cnt;
        }

        // get count values
        let cnt = fnGetCount(params['value']);
        if (cnt == 0) {
            cnt = 1;
        }
        
        const eventName = "multipleAdd_" + item['key'];
        let templateData = {
            multiple: {
                eventName: eventName,
                key: item['key'],
                title: item['title'],
                cssStyle: 'grid-template-columns: repeat(' + ('listRows' in item ? item['listRows'] : 1) + ', 1fr);',
                multipleItem: []
            }
        };

        //
        for (let i = 0; i < cnt; i += 1) {
            const key = item['key'] + '[' + i + ']';
            templateData['multiple']['multipleItem'][i] = {
                index: i,
                content: await this.make(item['value'], item => {
                    item['key'] = key;
                    if ('var' in item) {
                        item['key'] += item['var'].replace(/^([^\[]+)/, '[$1]');
                    }
                    return item;
                })
            }
        }
        
        // init add multiple event
        const itemValue = item['value'];
        const itemKey = item['key'];
        this.adminPage.addEvent(eventName, async e => {
            let current = $(e.currentTarget);
            let multipleBox = current.parent('.multiplePanel').prev('.multiple');
            const cnt = multipleBox.children('.multipleItem').length;
            const nextKey = itemKey + '[' + cnt + ']';
            let templateData = {
                multipleItem: {
                    index: cnt,
                    content: await this.make(itemValue, item => {
                        item['key'] = nextKey;
                        if ('var' in item) {
                            item['key'] += item['var'].replace(/^([^\[]+)/, '[$1]');
                        }
                        return item;
                    })
                }
            };

            const newItem = $(this.templ.makeTemplate(
                this.templates['multiple'],
                templateData, "multipleItem"
            ));
            newItem.hide();
            let appendAfter = current.attr('data-appendAfter');
            if (typeof appendAfter == 'string' && appendAfter.length > 0) {
                $(appendAfter).after(newItem);
                this.updateMultipleIndexes(multipleBox);
                current.attr('data-appendAfter', '');
            } else {
                multipleBox.append(newItem);
            }
            newItem.slideToggle('fast', () => {});
            
            this.adminPage.initEvents(multipleBox);
        });
        
        return this.templ.makeTemplate(templates['multiple'], templateData);
    }

    updateMultipleIndexes(multipleBox, updateForm)
    {
        const multipleVar = multipleBox.attr('data-field');

        if (typeof updateForm === 'undefined') {
            updateForm = this;
        }
        
        // set new indexes
        let multipleData = updateForm.get(multipleVar);
        let multipleDataNew = {};
        multipleBox.children('.multipleItem').each((newIndex, item) => {
            item = $(item);
            const oldIndex = item.attr("data-index");
            item.attr("data-index", newIndex);
            // update name variables in HTML form
            item.find('[data-field^="' + multipleVar + '\\[' + oldIndex + '\\]' + '"]')
                .each((pIndex, pItem) => {
                    pItem = $(pItem);
                    let oldVar = pItem.attr('data-field');
                    let newVar = oldVar.replace(
                        multipleVar + '[' + oldIndex + ']',
                        multipleVar + '[' + newIndex + ']'
                    );
                    pItem.attr('data-field', newVar);
                    const ttl = pItem.attr('title');
                    if (ttl !== undefined && ttl !== false) {
                        pItem.attr('title', newVar);
                    }
                    if (pItem.prev().attr('id') == 'title_' + oldVar) {
                        pItem.prev().attr('id', 'title_' + newVar);
                    }
                    if (pItem.next().attr('id') == 'value_' + oldVar) {
                        pItem.next().attr('id', 'value_' + newVar);
                    }
                });
            // update data
            multipleDataNew[newIndex] = multipleData[oldIndex];
        });
        // update data
        updateForm.unset(multipleVar);
        updateForm.set(multipleVar, multipleDataNew);
    }
}

// templates
let templates = {};

templates['field'] = `<!-- b[templ] { -->
<div class="fieldBlock">
    <div class="fieldTitle" id="title_<!-- v[key] -->"><!-- v[title] --></div>
        <a 
            nohref 
            class="fieldEditLink" 
            data-click="openEdit" 
            data-field="<!-- v[key] -->"
            data-field-actionName="<!-- v[actionName] -->"
            data-field-actionParams="<!-- v[actionParams] -->"
            data-field-type="<!-- v[type] -->" 
            title="<!-- v[key] -->"><!--&#9998;--><svg class="adminBuilderEditIcon">
            <use href="/admin/module/builder/images/feather-sprite.svg#edit"/>
        </svg></a>
        <!-- b[aboutField] { -->
            <a 
                nohref 
                class="fieldEditLink" 
                data-click="openAboutField" 
                data-field="<!-- v[key] -->"
                data-tooltip="<!-- v[aboutFieldContent] -->"><!--&#9998;--><svg class="adminBuilderEditIcon">
                <use href="/admin/module/builder/images/feather-sprite.svg#alert-octagon"/>
            </svg></a>
        <!-- } b[aboutField] -->
        <div class="fieldValue" id="value_<!-- v[key] -->"><!-- v[value] --></div>
    </div>
<!-- } b[templ] -->`;

templates['blockList'] = `<!-- b[templ] { -->
<div class="list<!-- v[css] -->">
<!-- b[blockTitle] { -->
<div class="title<!-- v[css] -->" data-blockState="<!-- v[blockState] -->"
    data-click="blockListOpenClose" style="cursor: pointer;">&#9776;
    <!-- v[blockTitle] --><span class="pm"><!-- v[pm] --> </span>
</div>
<!-- } b[blockTitle] -->
<!-- b[blockList] { -->
<div class="table<!-- v[css] -->" style="<!-- v[cssStyle] -->">
    <!-- v[fields] -->
</div>
<!-- } b[blockList] -->
</div>
<!-- } b[templ] -->`;

templates['multiple'] = `<!-- b[multiple] { -->
<div class="multiple" data-field="<!-- v[key] -->" style="<!-- v[cssStyle] -->">
    <!-- b[multipleItem] { -->
    <div class="multipleItem" data-index="<!-- v[index] -->" data-draggable="true">
        <div class="multipleItemPanel" style="grid-template-columns:repeat(5, 1fr)">
            <a nohref title="Переместить" draggable="true">
                <!--button class="btn">&#8597;</button-->
                <svg class="adminIcon">
                    <use href="/admin/module/builder/images/feather-sprite.svg#move"/>
                </svg>
            </a>
            <a nohref title="Добавить" data-click="multipleAdd">
                <!--button class="btn">&#9746;</button-->
                <svg class="adminIcon">
                    <use href="/admin/module/builder/images/feather-sprite.svg#plus"/>
                </svg>
            </a>
            <a nohref title="Удалить" data-click="multipleDelete">
                <!--button class="btn">&#9746;</button-->
                <svg class="adminIcon">
                    <use href="/admin/module/builder/images/feather-sprite.svg#delete"/>
                </svg>
            </a>
            <a nohref title="Дублировать" data-click="multipleDouble">
                <!--button class="btn">D</button-->
                <svg class="adminIcon">
                    <use href="/admin/module/builder/images/feather-sprite.svg#copy"/>
                </svg>
            </a>
            <a nohref title="Копировать блок на другую страницу" data-click="blockCopyTo">
                <!--button class="btn">&copy;</button-->
                <svg class="adminIcon">
                    <use href="/admin/module/builder/images/feather-sprite.svg#share"/>
                </svg>
            </a>
        </div>
        <!-- v[content] -->
    </div>
    <!-- } b[multipleItem] -->
</div>
<div class="multiplePanel">
    <button class="btn" data-click="<!-- v[eventName] -->"><!-- v[title] --> +</button>
</div>
<!-- } b[multiple] -->`;

// admin page generate

// constructor
class KAdminPage
{
    constructor()
    {
        this.movedItem = undefined;
        this.popup = new PopupMaker();
        this.events = {};
        this.blockEdited = false;
    }

    // methods
    addEvent(name, cb)
    {
        this.events[name] = cb;
    }

    initEvents(parent)
    {
        // init click events
        if (parent === undefined) {
            parent = $("#formEdit");
        }
        parent.find('[data-click]').unbind("click").click(e => {
            e.preventDefault();
            const eventName = $(e.currentTarget).attr('data-click');
            console.log("click:", eventName);
            if (eventName in this.events) {
                this.events[eventName](e);
            }
            else {
                console.log("click error:", eventName);
            }
        });

        // init drag & drop events
        parent.find('div.multipleItem > div.multipleItemPanel > a[draggable="true"]').each((index, el) => {
            el.removeEventListener("dragstart", this.events['dragStart']);
            el.addEventListener("dragstart", this.events['dragStart']);
            el.removeEventListener("dragover", e => e.preventDefault());
            el.addEventListener("dragover", e => e.preventDefault());
            el.removeEventListener("dragend", this.events['dragEnd']);
            el.addEventListener("dragend", this.events['dragEnd']);
            el.removeEventListener("dragleave", this.events['dragLeave']);
            el.addEventListener("dragleave", this.events['dragLeave']);
        });
        parent.find('div.multipleItem[data-draggable="true"]').each((index, el) => {
            el.removeEventListener("dragenter", this.events['dragEnter']);
            el.addEventListener("dragenter", this.events['dragEnter']);
            el.removeEventListener("dragover", e => e.preventDefault());
            el.addEventListener("dragover", e => e.preventDefault());
        });
        // drop events
        document.removeEventListener('drop', this.events['drop']);
        document.addEventListener('drop', this.events['drop']);

        tooltip();
    }

    async make(forms, data)
    {
        if (forms.length == 0) {
            return;
        }

        // make form
        let adminForm = new KAdminForm({
            ID: data['ID'],
            data: data['data'],
            templates: templates,
            adminPage: this
        });

        //
        this.addEvents(adminForm);

        //
        for (let i in forms) {
            let formEdit = await adminForm.make(forms[i]['params']);
            $(forms[i]['selector']).html(formEdit);
        }

        this.initEvents($('#formEdit'));
    }

    addEvents(adminForm)
    {
        // send form
        this.addEvent('send', e => {
            if ($(e.currentTarget).hasClass('active')) {
                adminForm.send();
            }
        });

        // open/close block
        this.addEvent('blockListOpenClose', e => {
            let ttl = $(e.currentTarget);
            let state = ttl.attr('data-blockState');
            let arrow = "";
            if (state == "open") {
                arrow = '&#9660;';
                ttl.next().slideToggle();
                state = "close";
            }
            else {
                arrow = '&#9650;';
                ttl.next().slideToggle();
                state = "open";
            }
            ttl.attr('data-blockState',state);
            ttl.find('span.pm').html(arrow);
        });

        // open field edit popup
        this.addEvent('openEdit', async e => {
            let link = $(e.currentTarget);
            let key = link.attr('data-field');
            const JQID = key.replace(/(\[|\])/g, "\\$1");
            let type = link.attr('data-field-type');
            let value = null;
            if (this.blockEdited !== false) {
                value = this.blockEdited['form'].get(key);
            } else {
                value = adminForm.get(key);
            }
            let fieldTitle = $('#title_' + JQID).html();

            let actionName = link.attr('data-field-actionName');
            let actionParams = link.attr('data-field-actionParams');
            let actionResult = undefined;
            if (actionName.length > 0) {
                if (actionParams.length > 0) {
                    actionParams = JSON.parse(decodeURIComponent(actionParams));
                } else {
                    actionParams = {};
                }
                actionResult = await adminForm.sendAction(actionName, actionParams);
            }

            let blockName = '';

            let formEdit = '';
            switch (type) {
                case 'string':
                    formEdit += '<input type="text" value="' + value.replace(/["]/g, '\\"') + '" id="fieldEdited" class="string">';
                    break;
                case 'integer':
                    formEdit += '<input type="text" value="' + value + '" id="fieldEdited" class="integer">';
                    break;
                case 'text':
                    formEdit += '<textarea id="fieldEdited" class="text">' + value + '</textarea>';
                    break;
                case 'text-editor':
                    formEdit += '<textarea id="fieldEdited" class="text tinyEditorMini">' + value + '</textarea>';
                    break;
                case 'blockSelect':
                case 'templateSelect':
                case 'select':
                    formEdit += '<select id="fieldEdited">';
                    formEdit += '<option value="">- Выбрать -</option>';
                    for (let i in actionResult) {
                        formEdit += '<option value="' + i + '"' + (value == i ? ' selected' : '') + '>' +
                            actionResult[i] + '</option>';
                    }
                    formEdit += '</select>';
                    break;
                case 'checkbox':
                    formEdit += '<div class="checkbox'+(value == 'yes' ? ' checked' : '') + '" ' +
                        'onclick="$(this).toggleClass(\'checked\');" id="fieldEdited">';
                    formEdit += '<div class="toggle"></div></div>';
                    break;
                case 'image':
                case 'file':
                case 'video':
                    formEdit += '<input type="hidden" value="" id="fieldEdited">';
                    formEdit += '<div class="selectImage">';
                    for (let path in actionResult) {
                        formEdit += '<div>';
                        if (type == 'image') {
                            formEdit += '<div style="word-break: break-word;"><strong>' + path.replace(/.+\// ,'') + '</strong></div>';
                            formEdit += `<img src="${path}" alt="" title="Выбрать изображение" 
                                    onclick="$('.selectImage img').removeClass('selected');
                                    $(this).addClass('selected');$('#fieldEdited').val('${path}');">
                                    <br><a href="${path}" target="_blank">Открыть</a>`;
                        } else if (type == 'video') {
                            formEdit += '<div style="word-break: break-word;"><strong>' + path.replace(/.+\// ,'') + '</strong></div>';
                            formEdit += `<div class="fileItem" onclick="$('.selectImage div').removeClass('selected');
                                            $(this).addClass('selected');
                                            $('#fieldEdited').val('${path}');">
                                <video poster="" id="" width="100%" height="auto" autoplay="autoplay" loop="loop" preload="auto" muted="false">
                                        <source src="${path}">
                                </video>
                                </div>`;
                        } else if (type == 'file') {
                            formEdit += `<div class="fileItem" onclick="$('.selectImage div').removeClass('selected');
                                    $(this).addClass('selected');
                                    $('#fieldEdited').val('${path}');">
                                    ${path.replace(/.+\// ,'')}</div>
                                    <br><a href="${path}" target="_blank">Открыть</a>`;
                        }
                        
                        formEdit += '</div>';
                    }
                    formEdit += '</div>';

                    // upload form
                    if ('uploadDir' in actionParams) {
                        formEdit += `<div style="margin-top: 40px;">
                            <form id="uploadFileForm">
                                <input type="hidden" name="uploadDir" value="${actionParams['uploadDir']}">
                                <input 
                                    type="file" 
                                    name="file" 
                                    id="uploadFileField" 
                                    style="display: none" 
                                    onchange="document.getElementById('uploadFileSelected').innerHTML = 'Выбрано: ' + this.value">
                                <label for="uploadFileField" class="uploadLabel">Добавить файл</label>
                            </form>
                        </div><div id="uploadFileSelected"></div>`;
                    }
                    break;
                case "date":
                    if (value == "") {
                        const dt = new Date();
                        const year = dt.getFullYear(),month = parseInt(dt.getMonth()) + 1, day = parseInt(dt.getDate());
                        value = year + '-' +
                                (month < 10 ? '0' + String(month) : String(month)) + '-' +
                                (day < 10 ? '0' + String(day) : String(day)) + ' 00:00:00';
                    }
                    formEdit += '<input type="text" class="date" value="' + value + '" ' +
                        'placeholder="DD-MM-YYYY 00:00:00" id="fieldEdited">';
                    break;
                case "blockEdit":
                    blockName = adminForm.get(key.replace(/\[params\]$/, '[name]'));
                    if (blockName.length == 0) {
                        formEdit = 'Выберите тип блока';
                    } else {
                        try {
                            let data = await FormHandler.send('', {
                                action: 'BlockParams',
                                blockName: blockName
                            });

                            // make form
                            if (this.blockEdited === false) {
                                console.log('init block edit');
                                // данные о редактируемом блоке
                                this.blockEdited = {
                                    key: key,
                                    link: link,
                                    editedKeys: []
                                };
                                this.blockEdited['form'] = new KAdminForm({
                                    data: typeof value != 'object' ? {} : JSON.parse(JSON.stringify(value)),
                                    templates: templates,
                                    adminPage: this
                                });
                            }
                            
                            formEdit += '<div class="content">\
                                <div id="formEditBlock">' + await this.blockEdited['form'].make(data['data']) + '</div>\
                            </div>';
                        } catch (error) {console.log('get block params error', blockName, error)};
                    }
                    break;
                case 'blockVersions':
                    blockName = adminForm.get(key.replace(/\[version\]$/, '[name]'));
                    if (blockName.length == 0) {
                        formEdit = 'Выберите тип блока';
                    } else {
                        try {
                            let data = await FormHandler.send('', {
                                action: 'BlockTemplateVersions',
                                blockName: blockName
                            });
                            formEdit += '<select id="fieldEdited">';
                            formEdit += '<option value="">- Выбрать -</option>';
                            for (let i in data['data']) {
                                formEdit += '<option value="' + i + '"' + (value == i ? ' selected' : '') + '>' +
                                data['data'][i] + '</option>';
                            }
                            formEdit += '</select>';
                        } catch (error) {
                            console.log('get block templates error', error)
                        }
                    }
                    break;
                case 'templateVersions':
                    const templateName = adminForm.get('template');
                    
                    if (templateName.length == 0) {
                        formEdit = 'Выберите шаблон страницы';
                    } else {
                        try {
                            let data = await FormHandler.send('', {
                                action: 'TemplateVersions',
                                templateName: templateName
                            });
                            formEdit += '<select id="fieldEdited">';
                            formEdit += '<option value="">- Выбрать -</option>';
                            for (let i in data['data']) {
                                formEdit += '<option value="' + i + '"' + (value == i ? ' selected' : '') + '>' +
                                data['data'][i] + '</option>';
                            }
                            formEdit += '</select>';
                        } catch (error) {
                            console.log('get page templates error', error);
                        }
                    }
                    break;
            }

            let aboutField = link.parent().find('[data-tooltip]');
            if (aboutField.length > 0) {
                let aboutFieldContent = escapeHtml(aboutField.attr('data-tooltip')).replace(/\n/g, '<br />');
                formEdit = `<div class="aboutField">${aboutFieldContent}</div>${formEdit}`;
            }

            let content = '<div class="form">' + formEdit + '</div>';
            this.popup.show({
                title: "Редактирование поля - " + fieldTitle,
                contents: content,
                showOnLoad: type == "image" ? "img" : undefined,
                buttons: {
                    confirm: {
                        title: "Сохранить",
                        click: async () => {
                            link.parent().css("border", "2px solid red");
                            let newValue = "";
                            switch(type) {
                                case 'text-editor':
                                    newValue = tinymce.activeEditor.getContent();
                                    break;
                                case 'checkbox':
                                    newValue = $('#fieldEdited').hasClass("checked") ? "yes" : "no";
                                    break;
                                default:
                                    let fieldEdited = $('#fieldEdited');
                                    if (fieldEdited.length > 0) {
                                        newValue = $('#fieldEdited').val();
                                    }
                            }

                            // refresh template form
                            if (type == 'templateSelect' && value != newValue) {
                                try {
                                    let data = await FormHandler.send('', {
                                        action: 'TemplateParams',
                                        templateName: newValue
                                    });
                                    adminForm.unset('templateData');
                                    $('#formEdit_template').html(await adminForm.make(data['data']));
                                    this.initEvents($('#formEdit_template'));
                                } catch (error) {
                                    console.log('get template params error', error);
                                }
                            }

                            // upload files
                            if (type == 'image' || type == 'file' || type == 'video') {
                                let uploadForm = $('#uploadFileForm');
                                if (uploadForm.length > 0) {
                                    let formData = new FormData(uploadForm[0]);
                                    const action = type == 'image' 
                                        ? 'UploadImage'
                                        : 'UploadFile';
                                    formData.append("action", action);
                                    try {
                                        let data = await FormHandler.send('', formData);
                                        if ('path' in data['data']) {
                                            newValue = data['data']['path'];
                                        }
                                    } catch (error) {
                                        console.log('upload file error', error);
                                    }
                                }
                            }

                            // clear block params
                            if (type == 'blockSelect' && value != newValue) {
                                adminForm.unset(key.replace(/\[name\]$/, '[params]'));
                            }
                            
                            // сохранение данных в режиме редактирования блока
                            if (this.blockEdited !== false) {
                                // сохраняем данные в общую форму
                                if (type == 'blockEdit') {
                                    let blockEditedValue = this.blockEdited['form'].get();
                                    console.log('save in main form', this.blockEdited['key'], blockEditedValue);
                                    adminForm.set(
                                        this.blockEdited['key'],
                                        blockEditedValue
                                    );
                                    newValue = '<div style="font-size: 12px"><b>Заполнено параметров:</b> ' + 
                                        getObjectPropsCount(blockEditedValue, true) + '</div>';
                                // сохраняем данные в форму редактирования блока
                                } else {
                                    console.log('set value for block', newValue);
                                    this.blockEdited['form'].set(key, newValue);
                                    if (this.blockEdited['editedKeys'].indexOf(key) === -1) {
                                        this.blockEdited['editedKeys'].push(key);
                                    }
                                }
                            } else {
                                adminForm.set(key, newValue);
                            }

                            if (actionResult !== undefined && actionResult[newValue] !== undefined) {
                                newValue = type == "image" 
                                    ? `<img src="${actionResult[newValue]}" alt="">`
                                    : actionResult[newValue];
                            } else if (type == 'image') {
                                newValue = `<img src="${newValue}" style="max-width: 100px">`;
                            }

                            $('#value_'+JQID).html(newValue);
                            this.popup.close();
                            $('.btnSend.disabled').removeClass('disabled').addClass('active');
                        }
                    }
                },
                _onClose: () => {
                    // обработка закрытия popup в режиме редактирования блока
                    if (this.blockEdited !== false) {
                        // выход из режима редактирования блока
                        if (type == 'blockEdit') {
                            console.log('close block edit');
                            this.blockEdited = false;
                        // возвращаемся в форму редактирование блока
                        } else {
                            console.log('return into block edit form');
                            this.blockEdited['link'].click();
                        }
                    }
                },
                _onShow: () => {
                    if (type == 'text-editor') {
                        console.log('on show load vis editor');
                        tinyMCEMini({
                            editor_selector: 'tinyEditorMini'
                        });
                    }
                }
            });

            this.initEvents($('#popupMaker'));

            // выделение красным цветом, ранее отредактированных полей
            if (type == 'blockEdit' && this.blockEdited !== false && this.blockEdited['editedKeys'].length > 0) {
                let editedLink = null;
                this.blockEdited['editedKeys'].forEach(value => {
                    editedLink = $('#formEditBlock').find('[data-field="' + value + '"]');
                    console.log('offset', editedLink.offset().top);
                    editedLink.parent().css("border", "2px solid red");
                    editedLink.parents(':hidden').show();
                });

                // позиционируем на последний отредактированный элемент
                let parentPos = $('.fancybox-slide')[0].getBoundingClientRect();
                let childrenPos = editedLink[0].getBoundingClientRect();
                let relativePos = childrenPos.top - parentPos.top;
                $('.fancybox-slide').scrollTop(relativePos - 20);
            }
        });

        // delete multiple item
        this.addEvent('multipleDelete', e => {
            const currentItem = $(e.currentTarget)
                .parent(".multipleItemPanel")
                .parent('.multipleItem');
            const currentIndex = currentItem.attr("data-index");
            const multipleBox = currentItem.parent('.multiple');
            const deleteVar = multipleBox.attr('data-field') + '[' + currentIndex + ']';
            currentItem.slideToggle('fast', () => {
                // delete item from DOM
                currentItem.remove();
                // reset items indexes and modify form data
                let updateForm = this.blockEdited !== false ? this.blockEdited['form'] : adminForm;
                updateForm.unset(deleteVar);
                adminForm.updateMultipleIndexes(multipleBox, updateForm);
                //
                $('.btnSend.disabled').removeClass('disabled').addClass('active');
            });
        });

        // create copy of multiple item
        this.addEvent('multipleDouble', e => {
            const currentItem = $(e.currentTarget)
                .parent(".multipleItemPanel")
                .parent('.multipleItem');
            const multipleBox = currentItem.parent('.multiple');

            const newItem = $(currentItem[0].outerHTML);
            newItem.hide();
            currentItem.after(newItem);
            newItem.slideToggle('fast', () => {
                // reset items indexes and modify form data
                let updateForm = this.blockEdited !== false ? this.blockEdited['form'] : adminForm;
                adminForm.updateMultipleIndexes(multipleBox, updateForm);
                this.initEvents(multipleBox);
                //
                $('.btnSend.disabled').removeClass('disabled').addClass('active');
            });
        });

        // add new multiple item after current item
        this.addEvent('multipleAdd', e => {
            const currentItem = $(e.currentTarget)
                .parent(".multipleItemPanel")
                .parent('.multipleItem');
            const multipleBox = currentItem.parent('.multiple');
            const multiplePanel = multipleBox.parent().find('.multiplePanel');
            const multipleVar = multipleBox.attr('data-field');
            const currentIndex = currentItem.attr('data-index');
            const addButton = multiplePanel.find('[data-click^="multipleAdd_"]');
            addButton.attr(
                'data-appendAfter',
                `[data-field="${multipleVar}"] [data-index="${currentIndex}"]`
            );
            addButton.trigger('click');
        });

        // send copy block to other page
        this.addEvent('blockCopyTo', e => {

            if ($('.btnSend').hasClass('active')) {
                this.popup.show({
                    title: 'Внимание!',
                    contents: `<span>Перед копированием блока необходимо 
                        сохранить и перезагрузить страницу</span>`,
                    buttons: false
                });
                return false;
            }

            const currentItem = $(e.currentTarget)
                .parent(".multipleItemPanel")
                .parent('.multipleItem');
            const currentIndex = currentItem.attr("data-index");

            let content = `<input type="text" name="" placeholder="Укажите ID страницы" id="blockCopyTo">
                <div id="blockCopyToResult"></div>`;
            this.popup.show({
                title: "Копирование блока",
                contents: content,
                buttons: {
                    confirm: {
                        title: "Копировать блок на страницу",
                        click: async () => {
                            let copyTo = $('#blockCopyTo').val();
                            if (copyTo.length > 0) {
                                try {
                                    let data = await FormHandler.send('', {
                                        action: "BlockCopyTo",
                                        copyFrom: adminForm.ID,
                                        blockNum: currentIndex,
                                        copyTo: copyTo
                                    });
                                    let resultText = '<span style="color: red">Произошла ошибка</span>';
                                    if (data['type'] == 'success') {
                                        let contents = '';
                                        copyTo = copyTo.replace(/\s/g, '');
                                        copyTo = copyTo.split(',');
                                        copyTo.forEach(copyToID => {
                                            console.log(copyToID, copyToID.length);
                                            if (copyToID.length > 0) {
                                                contents += `<a href="/admin/builder/detail?ID=${copyToID}" target="_blank">открыть страницу</a><br />`
                                            }
                                        });
                                        this.popup.show({
                                            title: 'Блок успешно скопирован',
                                            contents: contents,
                                            buttons: false
                                        });
                                    }
                                    $('#blockCopyToResult').html(resultText);
                                } catch (error) {
                                    $('#blockCopyToResult').html(error);
                                    console.log('block copy to error', error);
                                }
                            }
                        }
                    }
                }
            });
            
        });

        // drag & drop multiple item
        this.addEvent('dragStart', e => {
            e.dataTransfer.effectAllowed = 'move';
            this.movedItem = $(e.currentTarget).parent().parent();
            this.movedItem.css('opacity', 0.5);
        });
        this.addEvent('dragEnter', e => {
            e.preventDefault();
            //
            const targetItem = $(e.currentTarget);
            if (
                window._builderDragEnterBusy === true
                && !targetItem.hasClass('multipleItem')
            ) {
                return false;
            }
            const targetIndex = parseInt(targetItem.attr('data-index'));
            const movedIndex = parseInt(this.movedItem.attr('data-index'));
            const multipleVar = this.movedItem.parent().attr('data-field');
            if (movedIndex != targetIndex && 
                targetItem.parent().attr('data-field') == multipleVar) {
                //if (this.movedItem.position().top <= targetItem.position().top) {
                window._builderDragEnterBusy = true;
                setTimeout(() => {
                    if (movedIndex < targetIndex) {
                        targetItem.after(this.movedItem);
                    } else {
                        targetItem.before(this.movedItem);
                    }
                    const multipleBox = this.movedItem.parent();
    
                    // reset items indexes and modify form data
                    let updateForm = this.blockEdited !== false ? this.blockEdited['form'] : adminForm;
                    adminForm.updateMultipleIndexes(multipleBox, updateForm);
                    window._builderDragEnterBusy = false;
                }, 300);
            }
            
            return true;
        });
        this.addEvent('dragLeave', e => {
            e.preventDefault();
            return true;
        });
        this.addEvent('dragEnd', e => {
            this.movedItem.css('opacity', 1);
        });
        this.addEvent('drop', e => {
            //const multipleBox = this.movedItem.parent();

            // reset items indexes and modify form data
            //let updateForm = this.blockEdited !== false ? this.blockEdited['form'] : adminForm;
            //adminForm.updateMultipleIndexes(multipleBox, updateForm);

            //
            $('.btnSend.disabled').removeClass('disabled').addClass('active');
        });

        this.addEvent('openAboutField', e => {
            window._app['builderTooltipFix'] = true;
        });
    }
}

// init
$(() => {
    if (typeof window._app['pageForms'] !== 'undefined') {
        let adminPage = new KAdminPage();
        adminPage.make(
            window._app['pageForms'],
            window._app['pageData']
        );

        tooltip();
    }
});