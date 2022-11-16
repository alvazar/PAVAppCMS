<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class BlockParams extends AJAXAction
{
    public function run(array $data = []): array
    {
        $blockName = $data['blockName'] ?? '';

        return $this->app->get('Frontend\Page\Blocks')
                          ->getByName($blockName)
                          ->meta()
                          ->params();
    }
}
