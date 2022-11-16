<?php
namespace App\Frontend\Page\Templates\Main;

use App\Frontend\Page\Templates\Template;

class MainTemplate extends Template
{
    protected function init(): void
    {
        $this->meta->name('Основной шаблон для страниц');
        $this->meta->template('public/main');

        $this->meta->addParam([
            'title' => 'Верхнее меню',
            'type' => 'block-list',
            'css' => '',
            'value' => [
                [
                    'title' => 'Выбрать блок данных',
                    'type' => 'select',
                    'var' => 'templateData[topMenu]',
                    'actionName' => 'ListDataBlockItems'
                ]
            ]
        ]);
    }

    public function preload(array $data = []): void
    {
        if (!empty($data['topMenu'])) {
            $items = $this->app->get('DB\DataBlocks')->getByID($data['topMenu']);
            $this->app->data()->set('topMenu', $items['data']['items']);
        }
    }
}
