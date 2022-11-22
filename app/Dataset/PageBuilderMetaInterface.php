<?php
namespace App\Dataset;

interface PageBuilderMetaInterface
{
    public function addParam(array $params): PageBuilderMetaInterface;

    public function name(?string $name = null): string;
    
    public function template(?string $template = null): string;
    
    public function params(): array;
    
    public function clear(): PageBuilderMetaInterface;
}
