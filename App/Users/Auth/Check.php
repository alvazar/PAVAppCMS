<?php
namespace App\Users\Auth;

class Check
{
    public function isAuth(): bool
    {
        return !empty($_SESSION['user']);
    }

    public function isGroup(string $group): bool
    {
        return $this->isAuth() 
            && in_array($group, $_SESSION['user']['groups']);
    }
}
