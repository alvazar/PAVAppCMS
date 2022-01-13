<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListBlocks extends AJAXAction
{
    public function run(array $data = []): array
    {

        $result = $this->Site->model('Frontend\Page\Blocks')->getList();
        natcasesort($result);
        return $result;
    }
}