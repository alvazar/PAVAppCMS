<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class BlockTemplateVersions extends AJAXAction
{
    public function run(array $data = []): array
    {
        $blockName = $data['blockName'] ?? '';
        $templatePath = sprintf(
            '%s/resources/blocks/%s',
            $_SERVER['DOCUMENT_ROOT'],
            $this->app->get('Frontend\Page\Blocks')
                       ->getByName($blockName)
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
