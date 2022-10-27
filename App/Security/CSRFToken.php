<?php
namespace App\Security;

use App\AppUnit;

/*
Класс для генерации и проверки CSRF токена
 */

class CSRFToken extends AppUnit
{
    private $storageKey = 'csrf-token';

    public function makeToken(array $params = []): string
    {
        if (!empty($_SESSION[$this->storageKey])) {
            $token = $_SESSION[$this->storageKey];
        } else {
            $parts = sprintf('t_%s', time());
            $token = md5($parts);
            $_SESSION[$this->storageKey] = $token;
        }

        [$publicToken, $cookieToken] = str_split(
            $token,
            round(mb_strlen($token) / 2)
        );

        setcookie(
            $this->storageKey,
            $cookieToken,
            time() + 3600 * 24,
            '/'
        );

        return $publicToken;
    }

    public function checkToken(string $publicToken): bool
    {
        $isValid = false;
        
        if (
            !empty($_SESSION[$this->storageKey])
            && !empty($_COOKIE[$this->storageKey])
            ) {
            $isValid = $publicToken . $_COOKIE[$this->storageKey] === $_SESSION[$this->storageKey];
        }

        return $isValid;
    }
}