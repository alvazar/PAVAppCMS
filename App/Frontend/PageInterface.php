<?php
namespace App\Frontend;

use PAVApp\Core\ResultInterface;

interface PageInterface
{
    public function make(array $params = []): ResultInterface;
}
