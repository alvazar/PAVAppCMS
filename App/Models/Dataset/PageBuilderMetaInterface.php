<?php
namespace App\Models\Dataset;

interface PageBuilderMetaInterface
{
    public function addParam(array $params): PageBuilderMetaInterface;
    public function name(string $name = ''): string;
    public function template(string $template = ''): string;
    public function params(): array;
    public function clear(): PageBuilderMetaInterface;
}