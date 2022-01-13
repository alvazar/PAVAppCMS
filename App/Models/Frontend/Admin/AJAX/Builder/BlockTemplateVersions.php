<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class BlockTemplateVersions extends AJAXAction
{
    public function run(array $data = []): array
    {
        $blockName = $data['blockName'] ?? '';
        $templatePath = sprintf(
            '%s/resources/blocks/%s',
            $_SERVER['DOCUMENT_ROOT'],
            $this->Site->model('Frontend\Page\Blocks')
                       ->getByName($blockName)
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