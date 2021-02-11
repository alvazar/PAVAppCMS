<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use PAVApp\MVC\ModelInterface;

class EditData extends Controller
{
    protected function getModel(): ?ModelInterface
    {
        return new \App\Models\Admin\EditData();
    }

    protected function getParams(): array
    {
        return [
            'data' => $this->params['data'] ?? false
        ];
    }
}