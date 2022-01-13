<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class TemplateParams extends AJAXAction
{
    public function run(array $data = []): array
    {
        $templateName = $data['templateName'] ?? '';
        return $this->Site->model('Frontend\Page\Templates')
                          ->getByName($templateName)
                          ->meta()
                          ->params();
    }
}