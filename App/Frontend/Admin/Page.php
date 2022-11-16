<?php
namespace App\Frontend\Admin;

use App\AppUnit;
use App\Frontend\PageInterface;
use PAVApp\Core\Result;
use PAVApp\Core\ResultInterface;
use App\Config\Settings;

class Page extends AppUnit implements PageInterface
{
    public function make(array $params = []): ResultInterface
    {
        $page = $params['page'] ?? 'main';
        $subPage = $params['subPage'] ?? '';
        $subPage2 = $params['subPage2'] ?? '';
        $ver = $params['templateVer'] ?? 'v1';
        $app = $this->app;
        $Site = $this->Site;
        $app->data()->set('rootDir', Settings::ROOT_DIR);
        $result = new Result();

        $templatePath = sprintf(
            "%s%sresources/templates/admin/%s/template.php",
            $_SERVER['DOCUMENT_ROOT'],
            Settings::ROOT_DIR,
            $ver
        );

        if (!file_exists($templatePath)) {
            return $result;
        }
        
        // get page content
        $pagePath = sprintf(
            "%s%sresources/pages/admin/%s/template.php",
            $_SERVER['DOCUMENT_ROOT'],
            Settings::ROOT_DIR,
            !empty($subPage2)
                ? sprintf('%s/%s/%s', $page, $subPage, $subPage2) 
                : (!empty($subPage) ? sprintf('%s/%s', $page, $subPage) : $page)
        );

        $app->data()->set('pageContent', '');
        
        if (file_exists($pagePath)) {
            ob_start();
            require $pagePath;
            $app->data()->set('pageContent', ob_get_contents());
            ob_end_clean();
        }

        // make page
        ob_start();
        require $templatePath;
        $result->setData([
            'output' => ob_get_contents()
        ]);
        ob_end_clean();

        return $result;
    }
}
