// admin

// constructor
class KAdmin
{
    constructor()
    {
    }

    // methods
    getData()
    {
        return new Promise((resolve, reject) => {
            $.ajax(window._kadmin['rootDir'] + "admin/actions/process.php", {
                data: "action=getData",
                type: "post",
                dataType: "JSON",
                success: result => {
                    if (result['type'] == "success") {
                        resolve({
                            data: result['data'] instanceof Array ? {} : result['data'],
                            listValues: result['listValues']
                        });
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
        let sendData = {
            action: "edit",
            data: data
        };
        return new Promise((resolve, reject) => {
            $.ajax(window._kadmin['rootDir'] + "admin/actions/process.php", {
                data: sendData,
                type: "post",
                dataType: "JSON",
                success: result => result['type'] == "success" ? resolve() : reject(result['result']),
                error: () => {
                    $.ajax(window._kadmin['rootDir'] + "actions/process.php", {
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
        console.log(this.data);
        this.listValues = 'listValues' in params ? params['listValues'] : {};
        this.popup = new PopupMaker();
        this.templ = new Template();
        this.templates = 'templates' in params ? params['templates'] : {};
        this.adminPage = 'adminPage' in params ? params['adminPage'] : null;
    }

    // methods
    convertToObject(key, value)
    {
        //
        value = value !== undefined ? value : "";
        if (typeof value != "number") {
            value = value.replace(/\"/g, '\\"');
            value = '"'+value+'"';
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
                    }
                    else if (typeof obj[key] == "object" && 
                            typeof data[key] == "object") {
                        cb(cb, data[key], obj[key]);
                    }
                    else {
                        data[key] = obj[key];
                    }
                }
                else {
                    data[key] = obj[key];
                }
            }
        }
        updateData(updateData, this.data, obj);
        //console.log(JSON.stringify(this.data));
    }

    get(key)
    {
        const obj = this.convertToObject(key);
        const getData = (cb, data, obj) => {
            for (let key in obj) {
                if (typeof data !== "object" || key in data === false) {
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
        console.log('after delete', this.data);
    }

    send()
    {
        this.popup.show({
            title: "Отправка...",
            contents: "Идёт сохранение данных"
        });
        
        const admin = new KAdmin();
        admin.sendData(this.data).then(() => {
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

    make(fields, prepareItem, prepareField)
    {
        // make HTML form for edit data
        
        /*
        * title: <string> Название поля или списка полей.
        * type: <string> Тип поля или тип списка. Значения: block-list, multiple, string, text, integer, select, date, image.
        * var: <string> Имя переменной для хранения в базе.
        * value: <string|array> Значение поля или список полей.
        * blockState: <string> Состояние блока. Значения: open,close.
        * listRows: <integer> Кол-во рядов в списке.
        * css: <string> Название стиля css.
        * valueListName: <string> Название списка для полей с множественным выбором (select,checkbox,radio).
        * 
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
                    fieldHTML = this.makeBlockList(templParams);
                    break;
                // multiple
                case "multiple":
                    //fieldHTML = this.templ['fields'].get('multiple',templParams);
                    fieldHTML = this.makeMultiple(templParams);
                    break;
                // single field
                default:
                    //
                    if (item['valueListName'] !== undefined) {
                        let listValues = this.getListValues(item['valueListName']);
                        if (listValues[templParams['value']] !== undefined) {
                            templParams['value'] = listValues[templParams['value']];
                        }
                    }
                    //fieldHTML = this.templ['fields'].get('field',templParams);
                    fieldHTML = this.makeField(templParams);
                    break;
            }
            
            //
            form += prepareField !== undefined ? prepareField(fieldHTML) : fieldHTML;
        }
        
        //
        return form;
    }

    getListValues(listName)
    {
        let result = {};
        if (listName in this.listValues) {
            if (this.listValues[listName] instanceof Array) {
                //this.listValues[listName].sort();
            }
            for (let i in this.listValues[listName]) {
                result[i] = this.listValues[listName][i];
            }
        }
        return result;
    }

    makeField(params)
    {
        //
        let item = params['item'];
        let key = item['key'];
        let value = params['value'];
        if (item['type'] == 'image' && value != "") {
            value = '<img src="' + value + '" alt="">';
        }

        let templateData = {
            templ: {
                key: key,
                title: item['title'],
                valueListName: item['valueListName'] !== undefined ? item['valueListName'] : '',
                type: item['type'],
                value: value
            }
        };
        return this.templ.makeTemplate(this.templates['field'], templateData);
    }

    makeBlockList(params)
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
                    varValue = this.getListValues(varData[1].replace(':', ''))[varValue];
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
        fields += this.make(item['value'], params['prepareItem'], fieldHTML => {
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

    makeMultiple(params)
    {
        let item = params['item'];
        
        // get count values
        const cnt = params['value'].length > 0 ? params['value'].length : 1;
        
        const eventName = "multipleAdd_" + item['key'];
        let templateData = {
            multiple: {
                eventName: eventName,
                key: item['key'],
                title: item['title'],
                multipleItem: []
            }
        };

        //
        for (let i = 0; i < cnt; i += 1) {
            const key = item['key'] + '[' + i + ']';
            templateData['multiple']['multipleItem'][i] = {
                index: i,
                content: this.make(item['value'], item => {
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
        this.adminPage.addEvent(eventName, e => {
            let current = $(e.currentTarget);
            let multipleBox = current.parent('.multiplePanel').prev('.multiple');
            const cnt = multipleBox.children('.multipleItem').length;
            const nextKey = itemKey + '[' + cnt + ']';
            let templateData = {
                multipleItem: {
                    index: cnt,
                    content: this.make(itemValue, item => {
                        item['key'] = nextKey;
                        if ('var' in item) {
                            item['key'] += item['var'].replace(/^([^\[]+)/, '[$1]');
                        }
                        return item;
                    })
                }
            };
            multipleBox.append(this.templ.makeTemplate(this.templates['multiple'], templateData, "multipleItem"));
            this.adminPage.initEvents(multipleBox);
        });
        
        return this.templ.makeTemplate(templates['multiple'], templateData);
    }

    updateMultipleIndexes(multipleBox, updateData)
    {
        const multipleVar = multipleBox.attr('data-field');
        updateData = updateData === undefined ? false : true;
        
        // set new indexes
        let multipleData = this.get(multipleVar);
        let multipleDataNew = [];
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
            if (updateData) {
                multipleDataNew[newIndex] = multipleData[oldIndex];
            }
        });
        // update data
        if (updateData) {
            for (let i in multipleData) {
                multipleData[i] = multipleDataNew[i];
            }
        }
    }
}

// admin menu

// constructor
class KAdminMenu
{
    constructor()
    {
    }

    // methods
    getMenuItems()
    {
        return new Promise((resolve, reject) => {
            $.ajax(window._kadmin['rootDir'] + "admin/actions/process.php", {
                data: "action=getMenuItems",
                type: "post",
                dataType: "JSON",
                success: result => {
                    if (result['type'] == "success") {
                        resolve(result['result']);
                    }
                    else {
                        reject();
                    }
                }
            });
        });
    }

    make(items, attr)
    {
        attr = attr !== undefined ? ' ' + attr : '';
        let result = '<div' + attr + '>';
        let item = null,submenuID = 0;
        for (let i in items) {
            item = items[i];
            if (item['submenu'] !== undefined) {
                submenuID += 1;
                result += this._prepareItem(item, 'class="menuItem" id="submenu-' + String(submenuID) + '"');
                result += this.make(item['submenu'], 'class="subMenu"');
            }
            else {
                let pageID = this.getPageID(item['url']);
                result += this._prepareItem(item, 'class="menuItem" id="menuItem_' + pageID + '"');
            }
        }
        result += '</div>';
        return result;
    }

    _prepareItem(item,attr)
    {
        attr = attr !== undefined ? ' ' + attr : '';
        let result = '<div' + attr + '>';
        result += item['submenu'] !== undefined ? 
            '<a nohref class="subAction">' + item['title'] + '</a>' : 
            '<a href="' + window._kadmin['rootDir'] + item['url'] + '">' + item['title'] + '</a>';
        result += '</div>';
        return result;
    }

    submenu(link)
    {
        let sub = link.parent().next();
        //const isOpen = link.hasClass("open");
        sub.slideToggle();
        link.toggleClass('open');
    }
    
    menuItemSelect()
    {
        //const pageID = this.getPageID(window.location.pathname);
        const pageID = this.getPageID(window.location.href);
        $('#menuItem_' + pageID).addClass("active");
        let parent = $('#menuItem_' + pageID).parent();
        if (parent.hasClass("subMenu")) {
            this.submenu(parent.prev().find("a"));
        }
    }

    getPageID(url)
    {
        //let match = url.match(/[?&]page\=([^&]+)/i);
        let match = url.match(/page\/(.+)\/$/i);
        return match !== null ? match[1].replace("/", "_") : "";
    }

    //getPageID(url) {
    //    let pageID = url.replace(/.*(?:\/|)pages\/(.+)\/.*/ig,'$1').replace("/","_");
    //    return pageID;
    //}
}