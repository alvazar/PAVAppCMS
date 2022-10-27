<?php
namespace App\Frontend\Page\Blocks\Common;

use App\Frontend\Page\Blocks\Block;

class Embed extends Block
{
    protected function init(): void
    {
        $this->Meta->name('Embed');
        $this->Meta->template('common/embed');

        $this->Meta->addParam([
            'title' => 'Embed-код',
            'type' => 'text',
            'var' => 'embed'
        ]);
    }
}
