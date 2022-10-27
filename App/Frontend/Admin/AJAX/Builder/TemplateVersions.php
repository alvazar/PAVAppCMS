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
            $this->Site->model('Frontend\Page\Templates')
                       ->getByName($templateName)
                       ->meta()
                       ->template()
        );

        $result = [];
        $DirObj = dir($templatePath);
        while ($item = $DirObj->read()) {
            if ($item !== '.' && $item !== '..') {
                $result[$item] = $item;
            }
        }
        $DirObj->close();

        return $result;
    }
}