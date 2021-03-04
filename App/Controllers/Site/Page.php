<?php
namespace App\Controllers\Site;

use App\Controllers\Controller;
use PAVApp\MVC\ViewInterface;

class Page extends Controller
{
    protected function getView(): ?ViewInterface
    {
        return new \App\Views\Site\Page();
    }

    protected function getParams(): array
    {
        return [
            'page' => $this->params['page'],
            'subPage' => $this->params['subPage'] ?? ''
        ];
    }
}