<?php
namespace App\Frontend\Page\Templates;

use App\AppUnit;
use App\Dataset\ContentResultInterface;
use App\Dataset\PageBuilderMetaInterface;

abstract class Template extends AppUnit implements TemplateInterface
{
    protected $meta;
    protected $result;
    protected $version;

    public function afterAppUnitInit(): void
    {
        $this->meta = $this->app->get('Dataset\PageBuilderMeta');
        $this->result = $this->app->get('Dataset\ContentResult');
        
        $this->meta->addParam([
            'title' => 'Версия шаблона',
            'type' => 'templateVersions',
            'var' => 'templateData[version]'
        ]);

        $this->init();

        $params = $this->meta->params();
        $this->meta->clear()->addParam([
            'title' => 'Параметры шаблона',
            'type' => 'block-list',
            'value' => $params
        ]);
    }

    public function preload(array $data = []): void
    {
        
    }

    public function make(array $data = [], string $content = ''): ContentResultInterface
    {
        $this->result->content($this->makeTemplate($data, $content));
        
        return $this->result;
    }

    public function meta(): PageBuilderMetaInterface
    {
        return $this->meta;
    }

    public function setVersion(string $version = ''): TemplateInterface
    {
        $this->version = $version;

        return $this;
    }

    protected function init(): void
    {
    }

    protected function makeTemplate(array $data = [], string $content = ''): string
    {
        $template = $this->meta->template();

        if ($template === '') {
            return '';
        }

        $ver = !empty($this->version) ? $this->version : 'v1';
        $templatePath = sprintf(
            '%s/resources/templates/%s/%s/template.php',
            $_SERVER['DOCUMENT_ROOT'],
            $template,
            $ver
        );

        if (!file_exists($templatePath)) {
            return '';
        }

        $result = '';
        $Site = $this->Site;
        $app = $this->app;
        ob_start();
        require $templatePath;
        $result = ob_get_clean();

        return $result;
    }
}
