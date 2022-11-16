<?php
namespace App\Frontend\Page;

use App\AppUnit;
use App\Dataset\ContentResultInterface;
use Throwable;

/*
Класс собирает страницу из шаблона и блоков
 */

class Builder extends AppUnit
{
    private $ABTestVersion;
    
    public function build(array $params = []): ContentResultInterface
    {
        $this->ABTestInit();
        $ABTestVersion = !empty($this->ABTestVersion) 
            ? (int) $this->ABTestVersion
            : 1;

        $templateName = sprintf('Frontend\Page\Templates\%s', $params['template']);
        $Templ = $this->app->model($templateName);
        $Templ->preload($params['templateData']);
        
        $blocks = $params['blocks'] ?? [];

        $content = '';

        if (!empty($blocks)) {

            foreach ($blocks as $blockData) {

                $blockABTest = !empty($blockData['abtest']) 
                    ? (int) $blockData['abtest'] 
                    // если версия теста у блока не указана, то показываем блок на всех версиях
                    : $ABTestVersion;
                
                if (
                    empty($blockData['active'])
                    || $ABTestVersion !== $blockABTest
                ) {
                    continue;
                }

                $blockName = sprintf('Frontend\Page\Blocks\%s', $blockData['name']);

                try {
                    $content .= sprintf(
                        '<a name="%s"></a>',
                        !empty($blockData['params']['linkAnchor'])
                            ? $blockData['params']['linkAnchor']
                            : mb_strtolower(preg_replace('/.+\\\/', '', $blockName))
                    );

                    $content .= $this->app->get($blockName)
                                     ->setVersion($blockData['version'] ?? '')
                                     ->make($blockData['params'] ?? [])
                                     ->content();
                } catch(Throwable $err) {}
            }
        }

        if (!empty($params['meta'])) {
            $this->setMetaData($params['meta']);
        }

        return $Templ->setVersion($params['templateData']['version'] ?? '')
                     ->make($params['templateData'], $content);
    }

    protected function setMetaData(array $params): void
    {
        if (!empty($params['seo_title'])) {
            $this->app->data()->set(
                'seo_title',
                $params['seo_title']
            );
        }

        if (!empty($params['seo_description'])) {
            $this->app->data()->set(
                'seo_description',
                $params['seo_description']
            );
        }
    }

    protected function ABTestInit(): void
    {
        // A/B тестирование
        try {
            $abverCodes = [1 => 'roistat_param1=basic', 2 => 'roistat_param1=review'];
            $abver = null;

            if (isset($_GET['roistat_param1'])) {
                if ($_GET['roistat_param1'] === 'basic') {
                    $abver = 1;
                } elseif ($_GET['roistat_param1'] === 'review') {
                    $abver = 2;
                }
            }

            $ABTest = $this->app->get('Tests\ABTest')->init([
                'url' => $_SERVER['REQUEST_URI'],
                'version' => $abver
            ]);
            
            if (!$ABTest->exists()) {
                return;
            }

            if ($abver === null) {
                $abverCode = $abverCodes[$ABTest->getTestVersion()];
                $redirectUrl = sprintf(
                    'https://%s', $_SERVER['HTTP_HOST']
                );
                
                [$urlPath, $queryString] = strpos($_SERVER['REQUEST_URI'], '?') !== false
                    ? explode('?', $_SERVER['REQUEST_URI'], 2)
                    : [$_SERVER['REQUEST_URI'], ''];
                
                $redirectUrl .= '/' . trim($urlPath, '/');
                $redirectUrl .= !empty($queryString) 
                    ? '?' . $queryString . '&' . $abverCode
                    : '?' . $abverCode;
                
                header(sprintf('Location: %s', $redirectUrl));
                exit;
            }

            $ABTest->setView();

            $this->ABTestVersion = $ABTest->getTestVersion();

            // add embeds
            $this->app->data()->add('pageBottom', $ABTest->getTestEmbed());
            
        } catch (\Throwable $err) {}
    }
}
