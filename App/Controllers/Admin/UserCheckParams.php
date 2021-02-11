<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use PAVApp\MVC\ModelInterface;

class UserCheckAuth extends Controller
{
    protected function getModel(): ?ModelInterface
    {
        return new \App\Models\Admin\UserCheckParams();
    }
}