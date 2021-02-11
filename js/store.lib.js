// timer

// constructor
class Timer
{
    constructor(remain, elementID)
    {
        this.dateEnd = new Date();
        remain = parseInt(remain);
        remain += 10; // add 10 sec for that to exactly ending after server round reset
        remain *= 1000; // convert to milliseconds
        this.dateEnd.setTime(this.dateEnd.getTime() + remain);
        this.elementID = elementID;
    }
    // methods
    run()
    {
        const currentDate = new Date();
        const tmx = this.dateEnd.getTime() - currentDate.getTime(); // get remain milliseconds
        const dayLength = 86400000; // milliseconds in day
        if (tmx <= 1000) {
            document.getElementById(this.elementID).innerHTML = "00:00:00";
            //window.location.reload();
            window.location = "./?game";
        }
        //else if (false) {
        else if (tmx > dayLength) {
            const dt = new Date(
                currentDate.getFullYear(),
                currentDate.getMonth(),
                currentDate.getDate()
            );
            const dtmx = this.dateEnd.getTime() - dt.getTime();
            const days = dtmx / dayLength;
            remainTime = Math.floor(days);
            let getDaysName = days => {
                const lastNum = parseInt(days.toString().substr(-1));
                const lastTwo = parseInt(days.toString().substr(-2));
                if (lastNum == 1 && lastTwo != 11) {
                    return "день";
                }
                else if (lastNum < 5 && !(lastTwo > 10 && lastTwo < 20)) {
                    return "дня";
                }
                else {
                    return "дней";
                }
            };
            document.getElementById(this.elementID).innerHTML = remainTime + ' ' + getDaysName(remainTime);
        }
        else {
            let seconds = Math.floor(tmx / 1000);
            let hours = Math.floor(seconds / 3600);
            if (hours > 0) {
                seconds -= hours * 3600;
            }
            let minutes = Math.floor(seconds / 60);
            if (minutes > 0) {
                seconds -= minutes * 60;
            }
            let remainTime = this.addZero(hours) + ":" + this.addZero(minutes) + ":" + this.addZero(seconds);
            document.getElementById(this.elementID).innerHTML = remainTime;
            setTimeout(() => this.run(), 1000);
        }
    }
    addZero(number)
    {
        return number < 10 ? "0" + String(number) : String(number);
    }
}

// scroll
function fscroll(scr, params)
{
    if (params === undefined) {
        params = {};
    }
    let options = {
        duration: "speed" in params ? params['speed'] : "slow"
    };
    if (params['cb'] !== undefined) {
        options['complete'] = params['cb'];
    }
    const paddingTop = "paddingTop" in params ? parseInt(params['paddingTop']) : 90;
    $("html").stop().animate({scrollTop:$(scr).offset().top - paddingTop},options);
}

// gallery
// constructor
class Gallery
{
    constructor(popup, items)
    {
        this.items = items;
        this.currentID = 1;
        this.popup = popup;
        
        // events
        $("[data-gallery='item']").click(e => {
            e.preventDefault();
            const ID = $(e.currentTarget).attr("data-item");
            this.show(ID);
        });
    }

    // methods
    next()
    {
        let nextID = this.currentID + 1;
        if (nextID > this.items.length) {
            nextID = 1;
        }
        this.show(nextID);
    }
    prev()
    {
        let prevID = this.currentID - 1;
        if (prevID < 1) {
            prevID = this.items.length;
        }
        this.show(prevID);
    }
    show(ID)
    {
        ID = parseInt(ID);
        
        let el = this.items[ID-1];
        let img = '<img src="'+el['detail']+'" alt="" title=""/>';
        $("#modalGallery [data-gallery='show']").html(img);
        
        this.currentID = ID;
        this.popup.show("modalGallery");
        
        // video autoresize
        if (ID == 1) {
            //embedAutoresize($('#popupMaker iframe'));
        }
        
        $("#popupMaker [data-gallery='next']").click(e => {
            e.preventDefault();
            this.next();
        });
        $("#popupMaker [data-gallery='prev']").click(e => {
            e.preventDefault();
            this.prev();
        });
    }
}

// video slider
// constructor
class Slider {
    constructor(items)
    {
        this.items = items;
        this.startID = $(window).width() < 620 ? 1 : 3;
        this.currentID = this.startID;
        this.pos = 0;
        this.prevObj = $("[data-slider='prev']");
        this.nextObj = $("[data-slider='next']");
        this.itemsObj = $("[data-slider='items']");
        this.itemsObj.css("transition","transform .2s ease-in-out");
        this.translateX = this.itemsObj.find("div").outerWidth() + ($(window).width() < 620 ? 50 : 20);
        
        // events
        this.nextObj.click(e => {
            e.preventDefault();
            if (this.nextObj.attr("data-state") != "inactive") {
                this.next();
            }
        });
        this.prevObj.click(e => {
            e.preventDefault();
            if (this.prevObj.attr("data-state") != "inactive") {
                this.prev();
            }
        });
        $("[data-slider='item']").click(e => {
            e.preventDefault();
            const itemID = $(e.currentTarget).attr("data-item");
            this.show(itemID);
        });
    }
    // methods
    next()
    {
        if (this.currentID + 1 <= this.getCount()) {
            this.currentID += 1;
            this.prevObj.attr("data-state", "active").removeClass("inactive");
            this.pos += this.translateX;
            this.itemsObj.css("transform", "translateX(-" + this.pos + "px)");
            if (this.currentID == this.getCount()) {
                this.nextObj.attr("data-state", "inactive").addClass("inactive");
            }
        }
    }
    prev()
    {
        if (this.currentID - 1 >= this.startID) {
            this.currentID -= 1;
            this.nextObj.attr("data-state", "active").removeClass("inactive");
            this.pos -= this.translateX;
            this.itemsObj.css("transform", "translateX(-" + this.pos + "px)");
            if (this.currentID == this.startID) {
                this.prevObj.attr("data-state", "inactive").addClass("inactive");
            }
        }
    }
    show(ID)
    {
        ID = parseInt(ID);
        let item = this.items[ID];
        if ("embed" in item && item['embed'] != "") {
            $("[data-slider='show'] iframe").attr("src", item['embed']);
            $("[data-slider='title']").html(item['title']);
        }
    }
    getCount()
    {
        let cnt = 0;
        for (let i in this.items) {
            cnt += 1;
        }
        return cnt;
    }
}

// popup
function IMPopup() {
    PopupMaker.prototype.constructor.call(this);
}
IMPopup.prototype = Object.create(PopupMaker.prototype);
IMPopup.prototype.constructor = IMPopup;
IMPopup.prototype.show = function(popup,params) {
    if (typeof popup == "string") {
        popup = $('div#'+popup);
    }
    if (params === undefined) {
        params = {};
    }
    params['other'] = popup.html();
    params['buttons'] = false;
    if (params['showOnLoad'] === undefined) {
        if (popup.find("img").length > 0) {
            params['showOnLoad'] = "img";
        }
    }
    PopupMaker.prototype.show.call(this,params);
}
IMPopup.prototype.pos = function() {
    PopupMaker.prototype.setWindowCenter.call(this,$("#popupMaker"));
}

// customize shares
IMShare_konkurs = function(params) {
    IMShare.prototype.constructor.call(this, params);
};
IMShare_konkurs.prototype = Object.create(IMShare.prototype);
IMShare_konkurs.prototype.constructor = IMShare_konkurs;
IMShare_konkurs.prototype.show = function(element) {
    let bt = '';
    if (element.id != "resultSocial") {
        bt = '<li><a nohref><i class="icons icons-share"></i></a></li>';
    }
    bt += '<li><a href="#" data-soc="vk"><i class="icons icons-vk"></i></a></li>\
        <li><a href="#" data-soc="fb"><i class="icons icons-facebook"></i></a></li>\
        <li><a href="#" data-soc="tw"><i class="icons icons-twitter"></i></a></li>\
        <li><a href="#" data-soc="ok"><i class="icons icons-odnoklassniki"></i></a></li>';
    element.innerHTML = bt;
}

// embed autoresize
function embedAutoresize(embed) {
    embed.load(() => {
        embed.css("height",Math.round(embed.width()/1.78)+"px");
    });
    $(window).unbind("resize").resize(() => {
        embed.css("height",Math.round(embed.width()/1.78)+"px");
    });
}

// auth
var E_Auth_Processing=false;
function E_JS_Auth_Try() {
    if(E_Auth_Processing) { return false; }
    E_Auth_Processing=true;
    $.post('/-Engine-/AJAX/auth/index.php',{mode:11,process_user_login:1,auth_user_login:$('#popupMaker #AUTH_USER_LOGIN').val(),auth_user_password:$('#popupMaker #AUTH_USER_PWD').val()},function(data){
        if(data!='') { $('#popupMaker #E_Auth_JS').empty().html(data); }
        E_Auth_Processing=false;
    });
}
