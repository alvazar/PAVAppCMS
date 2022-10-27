<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListDataBlocks extends AJAXAction
{
    public function run(array $data = []): array
    {
        return $this->Site->model('Frontend\Page\DataBlocks')->getList();
    }
}
