<?php
namespace App\Frontend\Page\Templates;

use App\Dataset\ContentResultInterface;
use App\Dataset\PageBuilderMetaInterface;

interface TemplateInterface
{
    public function make(array $data = [], string $content = ''): ContentResultInterface;
    
    public function meta(): PageBuilderMetaInterface;

    public function setVersion(string $version = ''): TemplateInterface;
}