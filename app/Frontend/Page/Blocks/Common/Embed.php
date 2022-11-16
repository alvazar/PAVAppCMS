<?php
namespace App\Frontend\Page\Blocks\Common;

use App\Frontend\Page\Blocks\Block;

class Embed extends Block
{
    protected function init(): void
    {
        $this->meta->name('Embed');
        $this->meta->template('common/embed');

        $this->meta->addParam([
            'title' => 'Embed-код',
            'type' => 'text',
            'var' => 'embed'
        ]);
    }
}
