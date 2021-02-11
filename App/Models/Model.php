<?php
namespace App\Models;

use PAVApp\MVC\ModelAbstract;
use PAVApp\Core\ResultInterface;

abstract class Model extends ModelAbstract
{
    public function apply(array $params = []): ResultInterface
    {
        return $this->Result;
    }
}