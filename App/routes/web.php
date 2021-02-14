<?php
use PAVApp\MVC\Route;

/* ADMIN ROUTES */

// get
Route::get(
    '/admin/',
    'App\Controllers\Admin\Page.actionDefault',
    ['page' => 'main']
);
Route::get(
    '/admin/page/{page}/,
    /admin/page/{page}/{subPage}/',
    'App\Controllers\Admin\Page.actionDefault'
);

// post
Route::post(
    '/admin/actions/process.php',
    'App\Controllers\Admin\PostActionsHandler.actionDefault'
);

//
Route::get('/', function () {
    Route::redirect('/admin/');
});