<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;
use Exception;

class BlockCopyTo extends AJAXAction
{
    public function run(array $data = []): array
    {
        $data['copyTo'] = (string) ($data['copyTo'] ?? '');
        $copyTo = explode(',', str_replace(' ', '', trim($data['copyTo'] ?? '0', ', ')));
        unset($data['copyTo']);
        foreach ($copyTo as $copyToID) {
            $data['copyTo'] = $copyToID;
            $this->copyTo($data);
        }

        return [];
    }

    public function copyTo(array $data = []): array
    {
        $copyFrom = (int) ($data['copyFrom'] ?? 0);
        $copyTo = (int) ($data['copyTo'] ?? 0);
        $blockNum = (int) ($data['blockNum'] ?? 0);

        $pagesModel = $this->Site->model('DB\Pages');
        $pageFrom = $pagesModel->getByID($copyFrom);
        $pageTo = $pagesModel->getByID($copyTo);

        if (empty($pageFrom)) {
            throw new Exception('Страница не найдена [1]');

        } elseif (empty($pageFrom['blocks'][$blockNum])) {
            throw new Exception('Блок не найден');

        } elseif (empty($pageTo)) {
            throw new Exception('Страница не найдена');
        }

        $block = $pageFrom['blocks'][$blockNum];
        $block['active'] = 0;
        $blocks = $pageTo['blocks'];
        $blocks[] = $block;
        
        //
        $saveParams = [
            'fields' => [
                'blocks' => $blocks,
                'dateModify' => date('Y-m-d H:i:s')
            ],
            'where' => [
                'ID' => $pageTo['ID']
            ]
        ];
        
        if (!$pagesModel->save($saveParams)) {
            throw new Exception('Ошибка при копирование блока');
        }

        return [];
    }
}