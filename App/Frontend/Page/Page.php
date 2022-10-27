<?php
namespace App\Frontend\Page;

use App\AppUnit;
use App\Frontend\PageInterface;
use PAVApp\Core\Result;
use PAVApp\Core\ResultInterface;
use App\Config\Settings;

class Page extends AppUnit implements PageInterface
{
    public function make(array $params = []): ResultInterface
    {
        $page = $params['page'] ?? '/';
        $subPage = $params['subPage'] ?? '';
        $ver = $params['templateVer'] ?? 'v1';
        $app = $this->app;
        $Site = $this->Site;
        $app->data()->set('rootDir', Settings::ROOT_DIR);
        $pageContent = '';

        try {
            $pagesModel = $app->get('DB\Pages');
            $whereParams = ['active' => 1];
            if (!empty($_GET['pageHash'])) {
                [$pageID, $h] = explode('.', $_GET['pageHash'], 2);
                if ($app->get('Info\PageHash')->checkHash((int) $pageID, $_GET['pageHash'])) {
                    unset($whereParams['active']);
                }
            }
            $pageData = $pagesModel->getList([
                'where' => [
                    'section' => !empty($subPage) ? $page : '',
                    'url' => !empty($subPage) ? $subPage : $page
                ] + $whereParams,
                'limit' => 1
            ])[0] ?? [];
        } catch (\Throwable $err) {}
        
        // page from builder
        if (!empty($pageData)) {
            try {
                $pageContent = $app->get('Frontend\Page\Builder')
                                   ->build($pageData)
                                   ->content();
            } catch(\Throwable $err) {}
        // page from template
        } else {
            // get page content
            $pagePath = sprintf(
                "%s%sresources/pages/public/%s/template.php",
                $_SERVER['DOCUMENT_ROOT'],
                Settings::ROOT_DIR,
                !empty($subPage) ? sprintf('%s/%s', $page, $subPage) : $page
            );

            ob_start();
            $app->data()->set('pageContent', '');
            if (file_exists($pagePath)) {
                require $pagePath;
                $app->data()->set('pageContent', ob_get_contents());
                $templatePath = sprintf(
                    "%s%sresources/templates/public/%s/template.php",
                    $_SERVER['DOCUMENT_ROOT'],
                    Settings::ROOT_DIR,
                    $ver
                );
                ob_clean();
            }
            if (file_exists($templatePath)) {
                require $templatePath;
                $pageContent = ob_get_contents();
            }
            ob_end_clean();
        }

        //
        $result = new Result();
        $result->setData([
            'output' => $pageContent
        ]);
        
        return $result;
    }
}
