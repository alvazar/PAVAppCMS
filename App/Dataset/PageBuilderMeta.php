<?php
namespace App\Dataset;

use App\AppUnit;

class PageBuilderMeta extends AppUnit implements PageBuilderMetaInterface
{
    protected $name = '';
    protected $template = '';
    protected $params = [];

    public function addParam(array $params): PageBuilderMetaInterface
    {
        $this->params[] = $params;

        return $this;
    }

    public function name(?string $name = null): string
    {
        if (isset($name)) {
            $this->name = $name;
        }

        return $this->name;
    }

    public function template(?string $template = null): string
    {
        if (isset($template)) {
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
