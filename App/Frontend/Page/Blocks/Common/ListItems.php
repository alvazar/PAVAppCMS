<?php
namespace App\Frontend\Page\Blocks\Common;

use App\Frontend\Page\Blocks\Block;

class ListItems extends Block
{
    protected function init(): void
    {
        $this->Meta->name('Список элементов');
        $this->Meta->template('common/list_items');

        $this->Meta->addParam([
            'title' => 'Заголовок',
            'type' => 'string',
            'var' => 'title',
        ]);

        $this->Meta->addParam([
            'title' => 'Элемент',
            'type' => 'multiple',
            'var' => 'items',
            'value' => [
                [
                    'title' => 'Заголовок',
                    'type' => 'text',
                    'var' => 'title'
                ],
                [
                    'title' => 'Текст',
                    'type' => 'text',
                    'var' => 'text'
                ]
            ]
        ]);
    }
}
