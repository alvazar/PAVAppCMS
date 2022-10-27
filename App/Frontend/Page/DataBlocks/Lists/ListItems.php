<?php
namespace App\Frontend\Page\DataBlocks\Lists;

use App\Frontend\Page\DataBlocks\DataBlock;

class ListItems extends DataBlock
{
    protected function init(): void
    {
        $this->Meta->name('Список элементов');

        $this->Meta->addParam([
            'title' => 'Элемент',
            'type' => 'multiple',
            'var' => 'items',
            'value' => [
                [
                    'type' => 'block-list',
                    'css' => '',
                    'listRows' => 2,
                    'value' => [
                        [
                            'title' => 'Название',
                            'type' => 'string',
                            'var' => 'name'
                        ],
                        [
                            'title' => 'Значение',
                            'type' => 'text',
                            'var' => 'value'
                        ]
                    ]
                ]
            ]
        ]);
    }
}
