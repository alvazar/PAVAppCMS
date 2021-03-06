class AppForm
{
    constructor()
    {
    }

    static insertData(formObj, data, pref)
    {
        for (let i in data) {
            /*const target = typeof pref != 'undefined' 
                ? pref + '[' + i + ']'
                : i;*/
            let target = i;
            if (typeof pref != 'undefined') {
                target = (
                    data instanceof Array 
                    && typeof data[i] != 'object'
                )
                    ? pref + '[]'
                    : pref + '[' + i + ']';
            }
            if (typeof data[i] == 'object') {
                FormHandler.insertData(formObj, data[i], target);
            } else {
                let field = formObj.find('[type!="file"][name = "' + target + '"]');
                if (field.length == 0) {
                    continue;
                }
                if (
                    field.prop('tagName') == 'INPUT'
                    && field.attr('type') == 'checkbox'
                ) {
                    field.each((index, el) => {
                        el = $(el);
                        if (el.val() == data[i]) {
                            el.prop('checked', true);
                        }
                    });
                } else {
                    field.val(data[i]);
                    // set content to tinymce
                    if (field.attr('data-editor') !== undefined) {
                        const fieldID = field.attr('id');
                        if (fieldID !== undefined) {
                            tinyMCE.get(fieldID).setContent(data[i]);
                        }
                    }
                }
            }
        }
    }

    static clearData(formObj)
    {
        formObj[0].reset();
        formObj.find('input[type="checkbox"]').prop('checked', false);
    }

    static send(path, sendData)
    {
        sendData = sendData !== undefined ? sendData : {};
        return new Promise((resolve, reject) => {
            let sendParams = {
                data: sendData,
                type: "post",
                dataType: 'JSON',
                success: data => {
                    if (data['type'] == 'success') {
                        resolve(data);
                    } else {
                        reject(('message' in data ? data['message'] : 'Произошла ошибка'));
                    }
                },
                error: error => reject(error)
            };
            if (sendData instanceof FormData) {
                sendParams['processData'] = false;
                sendParams['contentType'] = false;
            }
            $.ajax(path, sendParams);
        });
    }
}

class AppText
{
    constructor()
    {
    }

    static replaceInText(txt, data, pref)
    {
        for (let i in data) {
            const target = typeof pref != 'undefined' 
                ? pref + '[' + i + ']'
                : i;
            if (typeof data[i] == 'object') {
                txt = AppText.replaceInText(txt, data[i], target);
            } else {
                txt = AppText.replaceAll(target, data[i], txt);
            }
        }
        return txt;
    }

    static replaceAll(target, repl, txt)
    {
        const escapeRegExp = str => str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return txt.replace(new RegExp(escapeRegExp('{' + target + '}'), 'g'), repl);
    }
}

class AppEvents
{
    constructor(pref)
    {
        this.pref = pref !== undefined ? String(pref) : "click";
        this.events = {};
        this.states = {};
    }

    add(name, cb)
    {
        this.events[name] = cb;
    }

    init(parent)
    {
        // init click events
        if (parent === undefined) {
            parent = $("body");
        }
        parent.find('[data-'+this.pref+']').unbind("click").click(e => {
            e.preventDefault();
            const eventName = $(e.currentTarget).attr('data-' + this.pref);
            console.log("click:", eventName);
            if (eventName in this.events) {
                this.events[eventName](e);
                this.states[eventName] = true;
            }
            else {
                console.log("click error:", eventName);
            }
        });
    }

    isRunning(eventName)
    {
        return eventName in this.states;
    }

    clear(eventName)
    {
        return delete this.states[eventName];
    }
}

class AppPage
{
    static states = {};
    static scroll(scrollToItem, top)
    {
        top = top !== undefined ? parseInt(top) : 0;
        scrollToItem = $(scrollToItem);
        if (scrollToItem.length > 0) {
            let scrollItemTop = scrollToItem.offset().top + top;
            $('html,body').stop().animate({ scrollTop: scrollItemTop }, 500);
        }
    }

    static runAfterLoad(selector)
    {
        return new Promise((resolve, reject) => {
            let items = $(selector);
            let itemsCnt = items.length;
            items.one('load', e => {
                itemsCnt -= 1;
                if (itemsCnt == 0) {
                    resolve();
                }
            }).each((index, el) => {
                if(el.complete) {
                    $(el).trigger('load');
                }
            });
        });
    }

    static runAfterVisible(selector, cb)
    {
        const stateKey = 'checkVisible_' + selector;
        let item = $(selector);
        const checkVisible = () => {
            if (stateKey in AppPage.states) {
                return false;
            }
            AppPage.states[stateKey] = true;
            setTimeout(() => {
                const windowBottom = window.pageYOffset + document.documentElement.clientHeight;
                let itemPosition = item.offset();
                if (windowBottom > itemPosition.top) {
                    cb(item);
                    document.removeEventListener("scroll", checkVisible);
                    window.removeEventListener("resize", checkVisible);
                }
                delete AppPage.states[stateKey];
            }, 200);
        };
        //
        checkVisible();
        document.addEventListener('scroll', checkVisible);
        window.addEventListener('resize', checkVisible);
    }

    static checkVisible(selector)
    {
        let item = $(selector);
        let isVisible = false;
        if (
            (
                item[0].getBoundingClientRect().top <= window.innerHeight
                && item[0].getBoundingClientRect().bottom >= 0
            ) 
            && getComputedStyle(item[0]).display != "none"
        ) {
            isVisible = true;
        }
        return isVisible;
    }
}

class AppOnEvent
{
    constructor()
    {
        this.preparies = {};
    }

    add(name, cb)
    {
        if (!(name in this.preparies)) {
            this.preparies[name] = [];
        }
        this.preparies[name].push(cb);
    }

    run(name, params)
    {
        if (name in this.preparies) {
            this.preparies[name].forEach(cb => cb(params));
        }
    }

    clear(name)
    {
        if (name in this.preparies) {
            this.preparies[name] = [];
        }
    }
}