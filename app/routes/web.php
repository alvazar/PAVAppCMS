<?php
use PAVApp\MVC\Route;
use App\AppHandler;
use App\Dataset\AppData;
use PAVApp\Core\Result;
use App\Users\User;

//
$appData = new AppData();
$app = new AppHandler($appData);
$user = new User();

$fnNeedAdminAuth = function ($cb) use($app, $user) {
    return $user->check()->isGroup('admin')
        ? $cb()
        : $app->get('Frontend\Admin\Page')->make([
            'page' => 'auth',
            'templateVer' => 'auth'
        ]);
};
$fnNeedAuth = function ($cb) use($app, $user) {
    return $user->check()->isAuth()
        ? $cb()
        : $app->get('Frontend\Admin\Page')->make([
            'page' => 'auth',
            'templateVer' => 'auth'
        ]);
};

/* admin routes */

// get
Route::get(
    '/admin,
    /admin/{page},
    /admin/{page}/{subPage},
    /admin/{page}/{subPage}/{subPage2}',
    function ($params) use($app, $fnNeedAdminAuth) {
        return $fnNeedAdminAuth(function () use($app, $params) {
            return $app->get('Frontend\Admin\Page')->make($params);
        });
    }
);

// post
Route::post(
    '/admin/actions/process.php',
    function ($params) use($app, $user) {

        if (!$user->check()->isGroup('admin')) {
            exit('Not authorized');
        }

        $cl = sprintf('Frontend\Admin\AJAX\%s', $params['action']);
        $AJAXResponse = $app->get('Actions\AJAXActionHandler')->run($cl, $params);
        $result = new Result();
        $result->setData([
            'output' => $AJAXResponse->getResponseJSON()
        ]);

        return $result;
    }
);
Route::post(
    '/admin,
    /admin/{page},
    /admin/{page}/{subPage},
    /admin/{page}/{subPage}/{subPage2}',
    function ($params) use($app, $user) {
        if (!$user->check()->isGroup('admin')) {
            exit('Not authorized');
        }

        return $app->get('Frontend\Admin\Page')->make($params);
    }
);

// auth process
Route::post(
    '/auth',
    function ($params) use($user) {
        !$user->check()->isAuth()
            && $user->login()->run(
                $params['login'] ?? '',
                $params['passw'] ?? ''
            );
        
        Route::redirect($params['urlFrom'] ?? '/');
    }
);
Route::get(
    '/logout',
    function ($params) use($user) {
        $user->check()->isAuth()
            && $user->logout()->run();
        
        Route::redirect($params['urlFrom'] ?? '/');
    }
);

/* fronted routes */
Route::get(
    '/,
    /{page},
    /{page}/{subPage}',
    function ($params) use($app) {
        return $app->get('Frontend\Page\Page')->make($params);
    }
);

Route::post(
    '/actions/process.php',
    'App\Actions\AJAXActionHandler.run'
);

//
if (file_exists(__DIR__ . '/web-local.php')) {
    require_once __DIR__ . '/web-local.php';
}
