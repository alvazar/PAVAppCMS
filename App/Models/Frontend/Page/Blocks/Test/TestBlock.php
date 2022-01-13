<?php
namespace App\Models\Frontend\Page\Blocks\Test;

use App\Models\Dataset\ContentResultInterface;
use App\Models\Frontend\Page\Blocks\Block;

class TestBlock extends Block
{
    public function make(array $data = []): ContentResultInterface
    {
        $result = '';
        if (!empty($data['title'])) {
            $result .= sprintf('<h1>%s</h1>', $data['title']);
        }

        if (!empty($data['title'])) {
            $result .= sprintf('<h2>%s</h2>', $data['description']);
        }

        $this->Result->content($result);

        return $this->Result;
    }

    protected function init(): void
    {
        $this->Meta->name('Тестовый блок');
        $this->Meta->template('product/test');

        $this->Meta->addParam([
            'title' => 'Заголовок',
            'type' => 'string',
            'var' => 'title'
        ]);
        $this->Meta->addParam([
            'title' => 'Описание',
            'type' => 'text',
            'var' => 'description'
        ]);
    }
}