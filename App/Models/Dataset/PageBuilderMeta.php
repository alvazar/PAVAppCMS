<?php
namespace App\Models\Dataset;

use App\Models\Model;

class PageBuilderMeta extends Model implements PageBuilderMetaInterface
{
    protected $name = '';
    protected $template = '';
    protected $params = [];

    public function addParam(array $params): PageBuilderMetaInterface
    {
        $this->params[] = $params;
        return $this;
    }

    public function name(string $name = ''): string
    {
        if ($name !== '') {
            $this->name = $name;
        }
        return $this->name;
    }

    public function template(string $template = ''): string
    {
        if ($template !== '') {
            $this->template = $template;
        }
        return $this->template;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function clear(): PageBuilderMetaInterface
    {
        $this->params = [];
        return $this;
    }
}