<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class TemplateVersions extends AJAXAction
{
    public function run(array $data = []): array
    {
        $templateName = $data['templateName'] ?? '';
        $templatePath = sprintf(
            '%s/resources/templates/%s',
            $_SERVER['DOCUMENT_ROOT'],
            $this->app->get('Frontend\Page\Templates')
                       ->getByName($templateName)
                       ->meta()
                       ->template()
        );

        $result = [];
        $dirObj = dir($templatePath);

        while ($item = $dirObj->read()) {

            if ($item !== '.' && $item !== '..') {
                $result[$item] = $item;
            }
        }

        $dirObj->close();

        return $result;
    }
}