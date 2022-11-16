<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class TemplateParams extends AJAXAction
{
    public function run(array $data = []): array
    {
        $templateName = $data['templateName'] ?? '';

        return $this->app->get('Frontend\Page\Templates')
                          ->getByName($templateName)
                          ->meta()
                          ->params();
    }
}