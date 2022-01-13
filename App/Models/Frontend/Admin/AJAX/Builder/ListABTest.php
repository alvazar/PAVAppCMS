<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListABTest extends AJAXAction
{
    public function run(array $data = []): array
    {
        return [1 => 'Вариант А', 2 => 'Вариант Б'];
    }
}