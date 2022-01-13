<?php
namespace App\Models\Frontend\Page;

use App\Models\Dataset\ContentResultInterface;
use App\Models\Model;
use Throwable;

/*
Класс собирает страницу из шаблона и блоков
 */

class Builder extends Model
{
    private $ABTestVersion;
    
    public function build(array $params = []): ContentResultInterface
    {
        $this->ABTestInit();
        $ABTestVersion = !empty($this->ABTestVersion) 
            ? (int) $this->ABTestVersion
            : 1;

        $templateName = sprintf('Frontend\Page\Templates\%s', $params['template']);
        $Templ = $this->Site->model($templateName);
        $Templ->preload($params['templateData']);
        
        $blocks = $params['blocks'] ?? [];

        $content = '';
        if (count($blocks) > 0) {
            foreach ($blocks as $blockData) {

                $blockABTest = !empty($blockData['abtest']) 
                    ? (int) $blockData['abtest'] 
                    // если версия теста у блока не указана, то показываем блок на всех версиях
                    : $ABTestVersion;
                
                if (empty($blockData['active'])) continue;
                elseif ($ABTestVersion !== $blockABTest) continue;

                $blockName = sprintf('Frontend\Page\Blocks\%s', $blockData['name']);
                try {
                    $content .= sprintf(
                        '<a name="%s"></a>',
                        !empty($blockData['params']['linkAnchor'])
                            ? $blockData['params']['linkAnchor']
                            : mb_strtolower(preg_replace('/.+\\\/', '', $blockName))
                    );
                    $content .= $this->Site->model($blockName)
                                     ->setVersion($blockData['version'] ?? '')
                                     ->make($blockData['params'] ?? [])
                                     ->content();
                } catch(Throwable $err) {}
            }
        }

        if (!empty($params['meta'])) {
            if (!empty($params['meta']['seo_title'])) {
                $this->Site->data()->set(
                    'seo_title',
                    $params['meta']['seo_title']
                );
            }
            if (!empty($params['meta']['seo_description'])) {
                $this->Site->data()->set(
                    'seo_description',
                    $params['meta']['seo_description']
                );
            }
        }

        return $Templ->setVersion($params['templateData']['version'] ?? '')
                     ->make($params['templateData'], $content);
    }

    public function ABTestInit(): void
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
            $ABTest = $this->Site->model('Tests\ABTest')->init([
                'url' => $_SERVER['REQUEST_URI'],
                'version' => $abver
            ]);
            
            if ($ABTest->exists()) {

                if ($abver === null) {
                    $abverCode = $abverCodes[$ABTest->getTestVersion()];
                    $redirectUrl = sprintf(
                        'https://%s', $_SERVER['HTTP_HOST']
                    );
                    
                    [$urlPath, $queryString] = strpos($_SERVER['REQUEST_URI'], '?') !== false
                        ? explode('?', $_SERVER['REQUEST_URI'], 2)
                        : [$_SERVER['REQUEST_URI'], ''];
                    
                    $redirectUrl .= '/'. trim($urlPath, '/');
                    $redirectUrl .= !empty($queryString) 
                        ? '?' . $queryString . '&' . $abverCode
                        : '?' . $abverCode;
                    
                    header(sprintf('Location: %s', $redirectUrl));
                    exit;
                }

                $ABTest->setView();

                $this->ABTestVersion = $ABTest->getTestVersion();

                // add embeds
                $this->Site->data()->add('pageBottom', "<script>
                $(() => {
                    window._app['onEvent'].add('afterOrderSuccess', params => {
                        if ('formObj' in params) {
                            delete params['formObj'];
                        }
                        params['url'] = window.location.href;
                        FormHandler.send('/ajax/check_param', {type: 'aborder', data: params, hash: '".$ABTest->getTestHash()."'});
                    
                    });                          
                    window._app['onEvent'].add('afterCallbackSuccess', params => {
                        FormHandler.send('/ajax/check_param', {type: 'aborder', data: params, hash: '".$ABTest->getTestHash()."'});
                    });
                });
                </script>");
                $this->Site->data()->add('pageBottom', $ABTest->getTestEmbed());
            }
        } catch (\Throwable $err) {}
    }
}