<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class DataBlockParams extends AJAXAction
{
    public function run(array $data = []): array
    {
        $dataBlockName = $data['dataBlockName'] ?? '';
        return $this->Site->model('Frontend\Page\DataBlocks')
                          ->getByName($dataBlockName)
                          ->meta()
                          ->params();
    }
}