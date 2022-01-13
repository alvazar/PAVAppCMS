<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListTemplates extends AJAXAction
{
    public function run(array $data = []): array
    {
        return $this->Site->model('Frontend\Page\Templates')->getList();
    }
}