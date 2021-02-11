<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Core\ResultInterface;

class UserCheckParams extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        foreach ($params as $key => $value) {
            $mt = sprintf("check%s", $key);
            if (!method_exists($this, $mt)) {
                $this->Result->setError('error param '.$key);
            } elseif (!$this->$mt($value)) {
                $this->Result->setError('error param '.$key);
            }
        }

        return $this->Result;
    }

    public function checkLogin(string $login): bool
    {
        return preg_match("/^[a-z0-9\_]{3,}$/u", (string) $login) === 1;
    }

    public function checkPassw(string $passw): bool
    {
        return preg_match("/^.{6,}$/u", (string) $passw) === 1;
    }

    public function checkEmail(string $email): bool
    {
        return preg_match("/^.+[@].+\..{2.10}$/u", (string) $email) === 1;
    }
}