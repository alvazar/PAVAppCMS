<?php
namespace App\Frontend\Page\DataBlocks;

use App\AppUnit;
use App\Frontend\Page\DataBlocks\DataBlockInterface;
use App\Dataset\PageBuilderMetaInterface;

abstract class DataBlock extends AppUnit implements DataBlockInterface
{
    protected $meta;

    public function afterAppUnitInit(): void
    {
        $this->meta = $this->app->get('Dataset\PageBuilderMeta');

        $this->init();

        $params = $this->meta->params();
        $this->meta->clear()->addParam([
            'title' => 'Данные',
            'type' => 'block-list',
            'var' => 'data',
            'value' => $params
        ]);
    }

    public function meta(): PageBuilderMetaInterface
    {
        return $this->meta;
    }

    protected function init(): void
    {
    }
}
