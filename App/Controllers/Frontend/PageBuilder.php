<?php
namespace App\Controllers\Frontend;

use App\Controllers\Controller;
use PAVApp\MVC\ViewInterface;

class PageBuilder extends Controller
{
    protected function getView(): ?ViewInterface
    {
        return new \App\Views\Frontend\PageBuidler();
    }

    protected function getParams(): array
    {
        return [
            'page' => $this->params['page'],
            'subPage' => $this->params['subPage'] ?? ''
        ];
    }
}
