// templates
let templates = {};

// events
let appEvent = new AppEvents();
appEvent.add('send', e => {
    const formID = $(e.currentTarget).attr('data-formID');
    let formObj = $('#' + formID);
    let formData = new FormData(formObj[0]);
    AppForm.send('/actions/process.php', formData).then(data => {
        let resp = $('#response');
        if (resp.length > 0) {
            if (resp[0].tagName.toLowerCase() == 'textarea') {
                resp.val(data['result']);
                resp.select();
            } else {
                resp.hide().html(data['result']).slideToggle();
            }
        }
    }).catch(error => console.log('error', error));
});

// init
$(document).ready(() => {
    appEvent.init();

    // Обработка кнопки "Назад" в браузере.
    $(window).on("popstate", function (e) {
        location.reload();
    });
});