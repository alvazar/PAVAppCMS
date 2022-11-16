<?php
namespace App\Users\Auth;

class Logout
{
    public function run(): void
    {
        if (array_key_exists('user', $_SESSION)) {
            unset($_SESSION['user']);
        }
    }
}
