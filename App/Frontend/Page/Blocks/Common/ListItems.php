<?php
namespace App\Frontend\Page\Blocks\Common;

use App\Frontend\Page\Blocks\Block;

class ListItems extends Block
{
    protected function init(): void
    {
        $this->meta->name('Список элементов');
        $this->meta->template('common/list_items');

        $this->meta->addParam([
            'title' => 'Заголовок',
            'type' => 'string',
            'var' => 'title',
        ]);

        $this->meta->addParam([
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
