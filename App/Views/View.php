<?php
namespace App\Views;

use PAVApp\MVC\ViewAbstract;
use PAVApp\Core\ResultInterface;

abstract class View extends ViewAbstract
{
    public function generate(array $data = []): ResultInterface
    {
        return $this->Result;
    }
}