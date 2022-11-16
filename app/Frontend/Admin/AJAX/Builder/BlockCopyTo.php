<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;
use Exception;

class BlockCopyTo extends AJAXAction
{
    protected const ERROR_MESSAGES = [
        'pageNotFound' => 'Страница не найдена',
        'blockNotFound' => 'Блок не найден',
        'blockCopy' => 'Ошибка при копирование блока',
    ];

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

        $pagesModel = $this->app->get('DB\Pages');
        $pageFrom = $pagesModel->getByID($copyFrom);
        $pageTo = $pagesModel->getByID($copyTo);

        if (empty($pageFrom)) {
            throw new Exception(self::ERROR_MESSAGES['pageNotFound']);

        } elseif (empty($pageFrom['blocks'][$blockNum])) {
            throw new Exception(self::ERROR_MESSAGES['blockNotFound']);

        } elseif (empty($pageTo)) {
            throw new Exception(self::ERROR_MESSAGES['pageNotFound']);
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
            throw new Exception(self::ERROR_MESSAGES['blockCopy']);
        }

        return [];
    }
}
