<?php
namespace App\Frontend\Page\Blocks;

use App\Dataset\ContentResultInterface;
use App\Dataset\PageBuilderMetaInterface;

interface BlockInterface
{
    public function make(array $data = []): ContentResultInterface;
    
    public function meta(): PageBuilderMetaInterface;

    public function setVersion(string $version = ''): BlockInterface;
}
