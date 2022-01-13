<?php
namespace App\Models\Frontend\Page\Templates;

use App\Models\Dataset\ContentResultInterface;
use App\Models\Dataset\PageBuilderMetaInterface;

interface TemplateInterface
{
    public function make(array $data = [], string $content = ''): ContentResultInterface;
    
    public function meta(): PageBuilderMetaInterface;

    public function setVersion(string $version = ''): TemplateInterface;
}