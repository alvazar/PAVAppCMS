<?php
namespace App\Frontend\Page\DataBlocks;

use App\AppUnit;
use App\Frontend\Page\DataBlocks\DataBlockInterface;
use App\Dataset\PageBuilderMetaInterface;

abstract class DataBlock extends AppUnit implements DataBlockInterface
{
    protected $Meta;

    public function afterAppUnitInit(): void
    {
        $this->Meta = $this->Site->model('Dataset\PageBuilderMeta');

        $this->init();

        $params = $this->Meta->params();
        $this->Meta->clear()->addParam([
            'title' => 'Данные',
            'type' => 'block-list',
            'var' => 'data',
            'value' => $params
        ]);
    }

    public function meta(): PageBuilderMetaInterface
    {
        return $this->Meta;
    }

    protected function init(): void
    {
    }
}
