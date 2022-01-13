/*
import * as app from '/view/pentaschool/js/pent_js.js';

// global init
window.is_adaptive = app.is_adaptive;
window.menuCatShow = app.menuCatShow;
window.prev_is_adaptive = app.prev_is_adaptive;
window.field_validator = app.field_validator;
window.clear_form = app.clear_form;
window.sendForm = app.sendForm;
window.FormHandler = app.FormHandler;
window.TextHandler = app.TextHandler;
window.EventsHandler = app.EventsHandler;
window.OnEventHandler = app.OnEventHandler;
window.PageHandler = app.PageHandler;
window.SliderHandler = app.SliderHandler;
window.Ecomm = app.Ecomm;
window.videosAutoResize = app.videosAutoResize;
window.tagsBlocks = app.tagsBlocks;
window.refreshSocialPhoto = app.refreshSocialPhoto;
window.owlSliderCount = app.owlSliderCount;
window.owlAutoplayOnVisible = app.owlAutoplayOnVisible;
window.owlStopAutoplayOnClick = app.owlStopAutoplayOnClick;
window.owlRefreshByParent = app.owlRefreshByParent;


async function initModule(module)
{
    try {
        let handler = await import(`./module/${module}`);
        if (typeof handler.moduleInit != 'undefined') {
            handler.moduleInit();
            console.log('init module', module);
        }
    } catch (error) {console.log('module error', error)};
}

let moduleName = $('#appBuild').attr('data-module');

console.log('moduleName', moduleName);
initModule(moduleName);
*/