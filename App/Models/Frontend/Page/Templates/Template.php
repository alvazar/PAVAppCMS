<?php
namespace App\Models\Frontend\Page\Templates;

use App\Models\Dataset\ContentResultInterface;
use App\Models\Dataset\PageBuilderMetaInterface;
use App\Models\Model;

abstract class Template extends Model implements TemplateInterface
{
    protected $Meta;
    protected $Result;
    protected $version;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->Meta = $this->Site->model('Dataset\PageBuilderMeta');
        $this->Result = $this->Site->model('Dataset\ContentResult');
        
        $this->Meta->addParam([
            'title' => 'Версия шаблона',
            'type' => 'templateVersions',
            'var' => 'templateData[version]'
        ]);

        $this->init();

        $params = $this->Meta->params();
        $this->Meta->clear()->addParam([
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
        $this->Result->content($this->makeTemplate($data, $content));
        
        return $this->Result;
    }

    public function meta(): PageBuilderMetaInterface
    {
        return $this->Meta;
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
        $result = '';
        
        $template = $this->Meta->template();
        if ($template !== '') {
            $ver = !empty($this->version) ? $this->version : 'v1';
            $templatePath = sprintf(
                '%s/resources/templates/%s/%s/template.php',
                $_SERVER['DOCUMENT_ROOT'],
                $template,
                $ver
            );
            if (file_exists($templatePath)) {
                $Site = $this->Site;
                ob_start();
                require $templatePath;
                $result = ob_get_clean();
            }
        }

        return $result;
    }
}