<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListYesNo extends AJAXAction
{
    public function run(array $data = []): array
    {
        return ['Нет', 'Да'];
    }
}