<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class PageTypeTaplink extends AJAXAction
{
    public function run(array $data = []): array
    {
        return ['Обычный блок', 'Слайдер в адаптиве', 'Блок с цветом и картинкой'];
    }
}