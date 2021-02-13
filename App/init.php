<?php

use App\Config\Namespaces as NSConfig;
use App\Config\Settings as AppSets;
use PAVApp\Core\Loader as Loader;
use PAVApp\MVC\Route;
use PAVApp\Core\Request;

ini_set("display_errors", true);
error_reporting(E_ALL);

//
require_once __DIR__.'/Config/Namespaces.php';
//require_once __DIR__.'/../PAVApp/Core/Loader.php';

// vendor autoload
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require_once __DIR__.'/../vendor/autoload.php';
}

// app autoload
spl_autoload_register(function ($cl) {
    (new Loader(NSConfig::NS_LIST))->start($cl);
});

// start routing
require_once __DIR__.'/routes/web.php';
Route::start(new Request(AppSets::ROOT_DIR));