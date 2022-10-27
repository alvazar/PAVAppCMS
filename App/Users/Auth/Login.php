<?php
namespace App\Users\Auth;

use App\Config\Users as UsersConfig;
use App\Config\UsersPassw as UsersPasswConfig;

class Login
{
    public function run(string $login, string $password): bool
    {
        $result = false;
        
        if (
            array_key_exists($login, UsersConfig::USERS)
            && array_key_exists($login, UsersPasswConfig::PASSW)
            && md5($password) === UsersPasswConfig::PASSW[$login]
        ) {
            $_SESSION['user'] = UsersConfig::USERS[$login];
            $result = true;
        }

        return $result;
    }
}
