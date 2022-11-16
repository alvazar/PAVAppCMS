<?php
namespace App\Frontend\Admin\AJAX;

use App\Actions\AJAXAction;

class MenuItems extends AJAXAction
{
    public function run(array $data = []): array
    {
        
        $result = [
            [
                'title' => 'Конструктор страниц',
                'url' => '',
                'submenu' => [
                    [
                        'title' => 'Страницы',
                        'url' => 'admin/builder/list'
                    ],
                    [
                        'title' => 'Данные',
                        'url' => 'admin/builder/data/list'
                    ]
                ]
            ]
        ];

        return $result;
    }
}
