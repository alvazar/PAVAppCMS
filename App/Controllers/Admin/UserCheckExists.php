<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use PAVApp\MVC\ModelInterface;

class UserCheckExists extends Controller
{
    protected function getModel(): ?ModelInterface
    {
        return new \App\Models\Admin\UserCheckExists();
    }
}