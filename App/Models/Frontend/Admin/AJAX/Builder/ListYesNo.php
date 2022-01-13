<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListYesNo extends AJAXAction
{
    public function run(array $data = []): array
    {
        return ['Нет', 'Да'];
    }
}