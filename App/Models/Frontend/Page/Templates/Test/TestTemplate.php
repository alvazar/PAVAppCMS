<?php
namespace App\Models\Frontend\Page\Templates\Test;

use App\Models\Frontend\Page\Templates\Template;

class TestTemplate extends Template
{
    protected function init(): void
    {
        $this->Meta->name('Тестовая страница');
        $this->Meta->template('test');

        $this->Meta->addParam([
            'title' => 'Блок текста',
            'type' => 'block-list',
            'css' => '',
            'value' => [
                [
                    'title' => 'Блок текста',
                    'type' => 'multiple',
                    'var' => 'templateData[texts]',
                    'value' => [
                        [
                            'title' => 'Текст',
                            'type' => 'text'
                        ]
                    ]
                ]
            ]
        ]);

        $this->Site->data()->add('add_css', 'contacts.css');
    }
}