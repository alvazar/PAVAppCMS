
class PopupMaker {
    constructor()
    {
        // first init
        $(() => {
            if (document.getElementById("popupMaker") === null) {
                $("body").append('<div id="popupMaker"></div>');
            }
        });

        this.onClose = null;
    }

    show(params)
    {
        //
        this.onClose = null;
        this.close();

        let popup = $("#popupMaker");
        popup.html("");
        
        // restore buttons
        $("#popupMaker .buttons .confirm").unbind();
        $("#popupMaker .buttons .cancel").unbind();
        $("#popupMaker .buttons").remove();
    
        // params
        if (params['width'] !== undefined) {
            popup.css("width",params['width']+"px");
        }
        if (params['height'] !== undefined) {
            popup.css("height",params['height']+"px");
        }
        if (params['title'] !== undefined) {
            popup.append('<div class="title">'+params['title']+'</div>');
        }
        if (params['contents'] !== undefined) {
            popup.append('<div class="contents">'+params['contents']+'</div>');
        }
        if (params['other'] !== undefined) {
            popup.append(params['other']);
        }
        if (params['_onClose'] !== undefined) {
            this.onClose = params['_onClose'];
        }
        let onShow = undefined;
        if (params['_onShow'] !== undefined) {
            onShow = params['_onShow'];
        }
        
        // buttons
        if (params['buttons'] === undefined) {
            params['buttons'] = {};
        }
        if (params['buttons'] !== false && params['buttons']['cancel'] !== false) {
            if (params['buttons']['cancel'] === undefined) {
                params['buttons']['cancel'] = {};
            }
            if (params['buttons']['cancel']['title'] === undefined) {
                params['buttons']['cancel']['title'] = "Отменить";
            }
            if (params['buttons']['cancel']['click'] === undefined) {
                params['buttons']['cancel']['click'] = () => this.close();
            }
        }
        if (params['buttons'] !== false) {
            popup.append('<div class="buttons"></div>');
            for (let bt in params['buttons']) {
                if (params['buttons'][bt] === false) {
                    continue;
                }
                if (params['buttons'][bt]['title'] !== undefined) {
                    popup.find(".buttons").append('<input type="submit" value="'+params['buttons'][bt]['title']+'" class="'+bt+'" />');
                }
                if (params['buttons'][bt]['click'] !== undefined) {
                    popup.find(".buttons ."+bt).click(params['buttons'][bt]['click']);
                }
            }
        }
        
        $.fancybox.open({
            src  : '#popupMaker',
            type : 'inline',
            opts : {
                touch: false,
                afterShow : onShow,
                beforeClose: () => {
                    if (this.onClose !== null) {
                        this.onClose();
                    }
                }
            }
        });
    }

    close()
    {
        $.fancybox.close();
    }
}