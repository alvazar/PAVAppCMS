<?php
namespace App\Users;

use App\Users\Auth\ {
    Login,
    Logout,
    Check
};

class User
{
    public function login(): Login
    {
        return new Login();
    }

    public function logout(): Logout
    {
        return new Logout();
    }

    public function check(): Check
    {
        return new Check();
    }
}
