<?php
namespace App\Frontend\Page\Blocks;

use App\AppUnit;
use App\Dataset\ContentResultInterface;
use App\Dataset\PageBuilderMetaInterface;

abstract class Block extends AppUnit implements BlockInterface
{
    protected $Meta;
    protected $Result;
    protected $version;

    public function afterAppUnitInit(): void
    {
        $this->Meta = $this->Site->model('Dataset\PageBuilderMeta');
        $this->Result = $this->Site->model('Dataset\ContentResult');

        $this->init();

        $params = $this->Meta->params();
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
        $this->Meta->clear()->addParam([
            'title' => 'Параметры блока',
            'type' => 'block-list',
            'value' => $params
        ]);
    }

    public function make(array $data = []): ContentResultInterface
    {
        $this->Result->content($this->makeTemplate($data));
        return $this->Result;
    }

    public function meta(): PageBuilderMetaInterface
    {
        return $this->Meta;
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
        $result = '';
        
        $template = $this->Meta->template();
        if ($template !== '') {
            $ver = !empty($this->version) ? $this->version : 'v1';
            $templatePath = sprintf(
                '%s/resources/blocks/%s/%s/template.php',
                $_SERVER['DOCUMENT_ROOT'],
                $template,
                $ver
            );
            if (file_exists($templatePath)) {
                $Site = $this->Site;
                $app = $this->app;
                ob_start();
                require $templatePath;
                $result = ob_get_clean();

                // indents
                if (!empty($data['indents'])) {
                    $indents = '';
                    if (!empty($data['indents']['top'])) {
                        $indents .= sprintf('margin-top: %s;', $data['indents']['top']);
                    }
                    if (!empty($data['indents']['bottom'])) {
                        $indents .= sprintf('margin-bottom: %s;', $data['indents']['bottom']);
                    }
                    preg_match('/\<(div|section).+?\>/i', $result, $match);

                    if (!empty($match[0])) {
                        $repl = $match[0];
                        if (preg_match('/style/', $match[0]) === 1) {
                            $repl = preg_replace('/style\=\"(.+?)\"/i', "style=\"{$indents}\${1}\"", $repl, 1);
                        } else {
                            $repl = mb_substr($repl, 0, -1).sprintf('style="%s">', $indents);
                        }
                        $result = preg_replace('/' . preg_quote($match[0], '/') . '/', $repl, $result, 1);
                    }
                }
            }
        }

        return $result;
    }
}