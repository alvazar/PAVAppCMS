<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

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