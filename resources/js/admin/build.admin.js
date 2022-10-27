// init
$(() => {
    let adminMenu = new KAdminMenu();
    console.log('admin menu', adminMenu);
    adminMenu.getMenuItems().then(menuItems => {
        console.log('menu items', menuItems);
        $("#leftMenu").html(adminMenu.make(menuItems,'class="menu"'));
        adminMenu.menuItemSelect();
        $(".subAction").click(e => adminMenu.submenu($(e.currentTarget)));
    }).catch(error => console.log('menu items error', error));
});
