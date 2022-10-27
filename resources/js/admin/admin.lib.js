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
                data: "action=MenuItems",
                type: "post",
                dataType: "JSON",
                success: result => {
                    console.log('get menu items', result);
                    if (result['type'] == "success") {
                        resolve(result['data']);
                    }
                    else {
                        console.log('menu items error', result);
                        reject();
                    }
                },
                error: (a, b, c) => console.log('ajax error', a, b, c)
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
        let match = url.match(/admin\/([^?]+)$/i);
        return match !== null ? match[1].replace(/\//g, "_") : "";
    }

    //getPageID(url) {
    //    let pageID = url.replace(/.*(?:\/|)pages\/(.+)\/.*/ig,'$1').replace("/","_");
    //    return pageID;
    //}
}