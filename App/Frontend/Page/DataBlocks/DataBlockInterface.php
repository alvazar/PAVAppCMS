<?php
namespace App\Frontend\Page\DataBlocks;

use App\Dataset\PageBuilderMetaInterface;

interface DataBlockInterface
{
    public function meta(): PageBuilderMetaInterface;
}
