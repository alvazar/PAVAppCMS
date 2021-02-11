<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Storage\DBStorage;
use PAVApp\Core\ResultInterface;
use PAVApp\MVC\Route;

class UserRegister extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        $login = $params['login'] ?? false;
        $passw = $params['passw'] ?? false;
        $passw2 = $params['passw2'] ?? false;

        // check user params
        $paramsCheck = Route::run(
            'App\Controller\Admin\UserCheckParams.actionDefault', [
                'login' => $login,
                'passw' => $passw
            ]
        );
        if ($paramsCheck->getError() !== '') {
            return $paramsCheck;
        }
        if ($passw2 !== $passw) {
            $this->Result->setError('repeat password error');
            return $this->Result;
        }

        // check user auth
        $authState = Route::run(
            'App\Controllers\Admin\UserCheckAuth.actionDefault'
        )->getData();
        if ($authState['isAuth']) {
            $this->Result->setError('user is auth');
            return $this->Result;
        }

        // check user exists in DB
        $userExists = Route::run(
            'App\Controllers\Admin\UserCheckExists.actionDefault',
            ['login' => $login]
        )->getData();
        if (!empty($userExists['userExists'])) {
            $this->Result->setError('user login exists');
            return $this->Result;
        }

        // add user in DB
        $DB = DBStorage::getInstance();
        $St = $DB->prepare("INSERT INTO kadmin_users (login, passw) VALUES(:login, :passw)");
        $res = $St->execute([
            'login' => $login,
            'passw' => $passw
        ]);
        if (!$res) {
            $this->Result->setError('register error');
        } else {
            $this->Result->setData(['userID' => $DB->lastInsertId()]);
        }

        return $this->Result;
    }
}