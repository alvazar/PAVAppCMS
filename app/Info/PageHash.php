<?php
namespace App\Info;

use App\AppUnit;

/*
Класс генерирует и проверяет хэш страницы по ID.
 */

class PageHash extends AppUnit
{
    private $secretKey = '&*$2%$#38767vhjg';

    public function getHash(int $ID): string
    {
        return sprintf('%d.%s', $ID, md5($ID . $this->secretKey . $ID));
    }

    public function checkHash(int $ID, string $hash): bool
    {
        return $this->getHash($ID) === $hash;
    }
}
