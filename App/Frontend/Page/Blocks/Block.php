<?php
namespace App\Frontend\Page\Blocks;

use App\AppUnit;
use App\Dataset\ContentResultInterface;
use App\Dataset\PageBuilderMetaInterface;

abstract class Block extends AppUnit implements BlockInterface
{
    protected $meta;
    protected $result;
    protected $version;

    public function afterAppUnitInit(): void
    {
        $this->meta = $this->app->get('Dataset\PageBuilderMeta');
        $this->result = $this->app->get('Dataset\ContentResult');

        $this->init();

        $params = $this->meta->params();
        $params = array_merge([
            [
                'title' => 'Якорь для скролла к блоку',
                'type' => 'string',
                'var' => 'linkAnchor',
                'aboutField' => sprintf(
                    'Якорь по умолчанию для данного блока: %s',
                    mb_strtolower(preg_replace('/.+\\\/', '', get_class($this)))
                )
            ],
            [
                'title' => 'Отступы',
                'type' => 'block-list',
                'css' => '',
                'listRows' => 2,
                'value' => [
                    [
                        'title' => 'Сверху',
                        'type' => 'integer',
                        'var' => 'indents[top]',
                        'aboutField' => 'например 20px, -20px'
                    ],
                    [
                        'title' => 'Снизу',
                        'type' => 'integer',
                        'var' => 'indents[bottom]',
                        'aboutField' => 'например 20px, -20px'
                    ],
                ]
            ]
        ], $params);

        $this->meta->clear()->addParam([
            'title' => 'Параметры блока',
            'type' => 'block-list',
            'value' => $params
        ]);
    }

    public function make(array $data = []): ContentResultInterface
    {
        $this->result->content($this->makeTemplate($data));

        return $this->result;
    }

    public function meta(): PageBuilderMetaInterface
    {
        return $this->meta;
    }

    public function setVersion(string $version = ''): BlockInterface
    {
        $this->version = $version;

        return $this;
    }

    protected function init(): void
    {
    }

    protected function makeTemplate(array $data = []): string
    {
        $template = $this->meta->template();

        if ($template === '') {
            return '';
        }

        $ver = !empty($this->version) ? $this->version : 'v1';
        $templatePath = sprintf(
            '%s/resources/blocks/%s/%s/template.php',
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

        // indents
        if (!empty($data['indents'])) {
            $result = $this->makeIndents($result, $data['indents']);
        }

        return $result;
    }

    protected function makeIndents(string $result, array $data): string
    {
        if (empty($data)) {
            return $result;
        }
        
        $indents = '';
        
        if (!empty($data['top'])) {
            $indents .= sprintf('margin-top: %s;', $data['top']);
        }

        if (!empty($data['bottom'])) {
            $indents .= sprintf('margin-bottom: %s;', $data['bottom']);
        }

        preg_match('/\<(div|section).+?\>/i', $result, $match);

        if (empty($match[0])) {
            return $result;
        }

        $repl = $match[0];

        if (preg_match('/style/', $match[0]) === 1) {
            $repl = preg_replace(
                '/style\=\"(.+?)\"/i',
                "style=\"{$indents}\${1}\"",
                $repl,
                1
            );
        } else {
            $repl = mb_substr(
                $repl,
                0,
                -1
            ) . sprintf('style="%s">', $indents);
        }

        $result = preg_replace(
            '/' . preg_quote($match[0], '/') . '/',
            $repl,
            $result,
            1
        );

        return $result;
    }
}
