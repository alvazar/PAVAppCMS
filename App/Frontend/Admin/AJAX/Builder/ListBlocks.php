<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListBlocks extends AJAXAction
{
    public function run(array $data = []): array
    {

        $result = $this->app->get('Frontend\Page\Blocks')->getList();
        natcasesort($result);

        return $result;
    }
}
