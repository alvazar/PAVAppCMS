<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Storage\DBStorage;
use PAVApp\Core\ResultInterface;
use PAVApp\MVC\Route;

class UserAuth extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        $login = $params['login'] ?? false;
        $passw = $params['passw'] ?? false;

        // check user params
        $paramsCheck = Route::run(
            'App\Controllers\Admin\UserCheckParams.actionDefault', [
                'login' => $login,
                'passw' => $passw
            ]
        );
        if ($paramsCheck->getError() !== '') {
            return $paramsCheck;
        }

        // check user auth
        $authState = Route::run('App\Controllers\Admin\UserCheckAuth.actionDefault')->getData();
        if ($authState['isAuth']) {
            $this->Result->setError("use is auth");
            return $this->Result;
        }

        // check user login and password
        $DB = DBStorage::getInstance();
        $St = $DB->prepare("SELECT ID FROM kadmin_users WHERE login = :login AND passw = :passw LIMIT 1");
        $St->execute([
            'login' => $login,
            'passw' => $passw
        ]);
        if ($St->rowCount() > 0) {
            $this->Result->setError("user auth error");
            return $this->Result;
        }

        // set user auth data
        $_SESSION['kadmin_user'] = [
            'login' => $login
        ];

        return $this->Result;
    }
}