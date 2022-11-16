<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListTemplates extends AJAXAction
{
    public function run(array $data = []): array
    {
        return $this->app->get('Frontend\Page\Templates')->getList();
    }
}
