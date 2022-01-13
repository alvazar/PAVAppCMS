<?php
namespace App\Models\Frontend\Page\Blocks;

use App\Models\Dataset\ContentResultInterface;
use App\Models\Dataset\PageBuilderMetaInterface;

interface BlockInterface
{
    public function make(array $data = []): ContentResultInterface;
    
    public function meta(): PageBuilderMetaInterface;

    public function setVersion(string $version = ''): BlockInterface;
}