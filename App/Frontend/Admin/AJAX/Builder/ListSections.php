<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListSections extends AJAXAction
{
    public function run(array $data = []): array
    {
        return [
            'program' => 'Программы',
            'category' => 'Категории',
            'page' => 'Произвольная страница'
        ];
    }
}
