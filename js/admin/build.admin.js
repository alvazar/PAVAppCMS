// templates
let templates = {};

templates['field'] = '\
<!-- b[templ] { -->\
<div class="fieldBlock">\
    <div class="fieldTitle" id="title_<!-- v[key] -->"><!-- v[title] --></div>'+
        '<a nohref class="fieldEditLink" data-click="openEdit" data-field="<!-- v[key] -->" '+
            'data-field-valueListName="<!-- v[valueListName] -->" '+
            'data-field-type="<!-- v[type] -->" title="<!-- v[key] -->">&#9998;</a>\
            <a nohref class="fieldEditLink" data-click="openView" data-field="<!-- v[key] -->" '+
            'data-field-valueListName="<!-- v[valueListName] -->" '+
            'data-field-type="<!-- v[type] -->" title="<!-- v[key] -->">&#128269;</a>\
        <div class="fieldValue" id="value_<!-- v[key] -->"><!-- v[value] --></div>\
    </div>\
<div class="fieldBG"></div>\
<!-- } b[templ] -->';

templates['blockList'] = '\
<!-- b[templ] { -->\
<div class="list<!-- v[css] -->">\
<!-- b[blockTitle] { -->\
<div class="title<!-- v[css] -->" data-blockState="<!-- v[blockState] -->" '+
    'data-click="blockListOpenClose" style="cursor: pointer;">&#9776; '+
    '<!-- v[blockTitle] --><span class="pm"><!-- v[pm] --> </span>\
</div>\
<!-- } b[blockTitle] -->\
<!-- b[blockList] { -->\
<div class="table<!-- v[css] -->" style="<!-- v[cssStyle] -->">\
    <!-- v[fields] -->\
</div>\
<!-- } b[blockList] -->\
</div>\
<!-- } b[templ] -->';

templates['multiple'] = '\
<!-- b[multiple] { -->\
<div class="multiple" data-field="<!-- v[key] -->">\
    <!-- b[multipleItem] { -->\
    <div class="multipleItem" data-index="<!-- v[index] -->" data-draggable="true">\
        <div class="multipleItemPanel">\
            <button class="btn" data-click="multipleDelete">&#9746;</button>\
            <button class="btn" draggable="true">&#8597;</button>\
        </div>\
        <!-- v[content] -->\
    </div>\
    <!-- } b[multipleItem] -->\
</div>\
<div class="multiplePanel">\
    <button class="btn" data-click="<!-- v[eventName] -->"><!-- v[title] --> +</button>\
</div>\
<!-- } b[multiple] -->';

// admin page generate

// constructor
class KAdminPage
{
    constructor()
    {
        this.movedItem = undefined;
        this.popup = new PopupMaker();
        this.events = {};
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
        parent.find('div.multipleItem > div.multipleItemPanel > button[draggable="true"]').each((index, el) => {
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
    }

    make()
    {
        this.getFields().then(fields => {
            if (fields.length == 0) {
                return;
            }
            let admin = new KAdmin();
            admin.getData().then(data => {
                // make form
                let adminForm = new KAdminForm({
                    data: data['data'],
                    listValues: data['listValues'],
                    templates: templates,
                    adminPage: this
                });
                adminForm.set("editTime", window._kadmin['editTime']); // set page opened time
                let formEdit = adminForm.make(fields);
                formEdit += '<button class="btnSend disabled" data-click="send">Сохранить</button>';
                $("#formEdit").html(formEdit);
                //
                this.addEvents(adminForm);
                this.initEvents();
            }).catch(error => console.log('error', error));
        }).catch(error => console.log('error', error));
    }

    getFields()
    {
        return new Promise((resolve, reject) => {
            const path = window._kadmin['rootDir'] + 'pages/' + window._kadmin['pageName'] + "/fields.json";
            $.ajax(path, {
                type: "get",
                dataType: "JSON",
                success: data => resolve(data),
                error: reject
            });
        });
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
        this.addEvent('openEdit', e => {
            let link = $(e.currentTarget);
            let key = link.attr('data-field');
            const JQID = key.replace(/(\[|\])/g, "\\$1");
            let type = link.attr('data-field-type');
            let value = adminForm.get(key);
            let fieldTitle = $('#title_' + JQID).html();
            let valueListName = link.attr('data-field-valueListName');
            let listValues = undefined;
            if (valueListName.length > 0) {
                listValues = adminForm.getListValues(valueListName);
            }
            
            let formEdit = '';
            switch (type) {
                case 'string':
                    formEdit += '<input type="text" value="' + value.replace(/["]/g, '\\"') + '" id="fieldEdited" class="string">';
                    break;
                case 'integer':
                    formEdit += '<input type="text" value="' + value + '" id="fieldEdited" class="integer">';
                    break;
                case 'text':
                case 'text-editor':
                    formEdit += '<textarea id="fieldEdited" class="text">' + value + '</textarea>';
                    break;
                case 'select':
                    formEdit += '<select id="fieldEdited">';
                    formEdit += '<option value="">- Выбрать -</option>';
                    for (let i in listValues) {
                        formEdit += '<option value="' + i + '"' + (value == i ? ' selected' : '') + '>' +
                            listValues[i] + '</option>';
                    }
                    formEdit += '</select>';
                    break;
                case 'checkbox':
                    formEdit += '<div class="checkbox'+(value == 'yes' ? ' checked' : '') + '" ' +
                        'onclick="$(this).toggleClass(\'checked\');" id="fieldEdited">';
                    formEdit += '<div class="toggle"></div></div>';
                    break;
                case 'image':
                    formEdit += '<input type="hidden" value="" id="fieldEdited">';
                    formEdit += '<div class="selectImage">';
                    for (let path in listValues) {
                        formEdit += '<div style="display: inline-block">';
                        formEdit += '<div><strong>' + path.replace(/.+\// ,'') + '</strong></div>';
                        formEdit += '<img src="' + listValues[path] + '" alt="" title="' + path.replace(/.+\//, '') + '" ' +
                            'onclick="$(\'.selectImage img\').removeClass(\'selected\');' +
                                '$(this).addClass(\'selected\');$(\'#fieldEdited\').val(\'' + path + '\');">';
                        formEdit += '</div>';
                    }
                    formEdit += '</div>';
                    break;
                case "date":
                    if (value == "") {
                        const dt = new Date();
                        const year = dt.getFullYear(),month = parseInt(dt.getMonth()) + 1, day = parseInt(dt.getDate());
                        value = (day < 10 ? '0' + String(day) : String(day)) + '-' +
                                (month < 10 ? '0' + String(month) : String(month)) + '-' +
                                year + ' 00:00:00';
                    }
                    formEdit += '<input type="text" class="date" value="' + value + '" ' +
                        'placeholder="DD-MM-YYYY 00:00:00" id="fieldEdited">';
                    break;
            }
            formEdit = '<div class="form">' + formEdit + '</div>';
            
            this.popup.show({
                title: "Редактирование поля - " + fieldTitle,
                contents: formEdit,
                showOnLoad: type == "image" ? "img" : undefined,
                buttons: {
                    confirm: {
                        title: "Изменить",
                        click: () => {
                            link.parent().css("border", "2px solid red");
                            let newValue = "";
                            switch(type) {
                                case 'text-editor':
                                    newValue = $('div.form div[contenteditable="true"]').html();
                                    break;
                                case 'checkbox':
                                    newValue = $('#fieldEdited').hasClass("checked") ? "yes" : "no";
                                    break;
                                default:
                                    newValue = $('#fieldEdited').val();
                            }
                            adminForm.set(key, newValue);
                            if (listValues !== undefined && listValues[newValue] !== undefined) {
                                newValue = type == "image" ? 
                                        '<img src="' + listValues[newValue] + '" alt="">' : 
                                        listValues[newValue];
                            }
                            $('#value_'+JQID).html(newValue);
                            this.popup.close();
                            $('.btnSend.disabled').removeClass('disabled').addClass('active');
                        }
                    }
                }
            });
            
            // ckeditor
            if (type == "text-editor") {
                ClassicEditor.create(document.querySelector('#fieldEdited'));
            }
        });

        // open field view popup
        this.addEvent('openView', e => {
            let link = $(e.currentTarget);
            let key = link.attr('data-field');
            const JQID = key.replace(/(\[|\])/g, "\\$1");
            let type = link.attr('data-field-type');
            let value = adminForm.get(key);
            let fieldTitle = $('#title_'+JQID).html();

            if (type == 'image' && value != "") {
                value = '<img src="' + value + '" alt="">';
            }

            value = value.replace(/\r\n|\n|\r/g, "<br>")
                         .replace(/\s\s/g, " &nbsp;")
                         .replace(/(^|[^"'=])((?:http[s]?\:\/\/(?:www\.|)|www\.)([^\s<>\"\']+))/gi,
                            (match, m1 , m2, m3, str) => {
                                let url = m2.search(/https/i) === -1 ? "https://" + m2 : m2;
                                if (m2.length > 60) {
                                    m2 = m2.substr(0,50) + '...';
                                }
                                return m1+'<a href="' + url + '" target="_blank" rel="nofollow">' + m2 + '</a>';
                            });
            let content = '<div class="openView">'+value+'</div>';
            
            this.popup.show({
                title: "Просмотр поля - " + fieldTitle,
                //width: 1200,
                contents: content,
                showOnLoad: type == "image" ? "img" : undefined,
                buttons: false
            });
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
                // delete from data
                adminForm.unset(deleteVar);
                // delete item from DOM
                currentItem.remove();
                // reset items indexes
                adminForm.updateMultipleIndexes(multipleBox);
                //
                $('.btnSend.disabled').removeClass('disabled').addClass('active');
            });
        });

        // drag & drop multiple item
        this.addEvent('dragStart', e => {
            e.dataTransfer.effectAllowed = 'move';
            this.movedItem = $(e.target).parent().parent();
            this.movedItem.css('opacity', 0.5);
        });
        this.addEvent('dragEnter', e => {
            e.preventDefault();
            //
            const targetItem = $(e.target);
            if (!targetItem.hasClass('multipleItem')) {
                return false;
            }
            const targetIndex = parseInt(targetItem.attr('data-index'));
            const movedIndex = parseInt(this.movedItem.attr('data-index'));
            const multipleVar = this.movedItem.parent().attr('data-field');
            if (movedIndex != targetIndex && 
                targetItem.parent().attr('data-field') == multipleVar) {
                const multipleBox = this.movedItem.parent();
                multipleBox.children('.dropZone').remove();
                const dropZone = '<div class="dropZone"></div>';
                if (this.movedItem.position().top <= targetItem.position().top) {
                    targetItem.after(dropZone);
                }
                else {
                    targetItem.before(dropZone);
                }
                // set drop zone events
                multipleBox.children('.dropZone')[0].addEventListener('dragover', e => e.preventDefault());
                multipleBox.children('.dropZone')[0].addEventListener('dragenter', e => {
                    e.preventDefault();
                    $(e.target).addClass('dropZoneHover');
                    return true;
                });
                multipleBox.children('.dropZone')[0].addEventListener('dragleave', e => {
                    e.preventDefault();
                    $(e.target).removeClass('dropZoneHover');
                    return true;
                });
                multipleBox.children('.dropZone')[0].addEventListener('drop', this.events['drop']);
            }
            
            return true;
        });
        this.addEvent('dragLeave', e => {
            e.preventDefault();
            return true;
        });
        this.addEvent('dragEnd', e => {
            const multipleBox = this.movedItem.parent();
            multipleBox.children('.dropZone').remove();
            this.movedItem.css('opacity', 1);
        });
        this.addEvent('drop', e => {
            const targetItem = $(e.target);
            if (!targetItem.hasClass('dropZone')) {
                return false;
            }
            // move item
            targetItem.replaceWith(this.movedItem);
            //
            const multipleBox = this.movedItem.parent();
            adminForm.updateMultipleIndexes(multipleBox, true);

            //
            $('.btnSend.disabled').removeClass('disabled').addClass('active');
        });
    }
}

// init
$(document).ready(() => {
    let adminMenu = new KAdminMenu();
    adminMenu.getMenuItems().then(menuItems => {
        $("#leftMenu").html(adminMenu.make(menuItems,'class="menu"'));
        adminMenu.menuItemSelect();
        $(".subAction").click(e => adminMenu.submenu($(e.currentTarget)));
    }).catch(error => console.log('menu items error', error));

    if ($("#formEdit").length > 0 && window._kadmin['pageName'] != "") {
        let adminPage = new KAdminPage();
        adminPage.make();
    }
});