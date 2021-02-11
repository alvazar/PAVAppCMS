<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use PAVApp\MVC\ViewInterface;

class Page extends Controller
{
    protected function getView(): ?ViewInterface
    {
        return new \App\Views\Admin\Page();
    }

    protected function getParams(): array
    {
        return [
            'page' => $this->params['page'],
            'subPage' => $this->params['subPage'] ?? ''
        ];
    }
}