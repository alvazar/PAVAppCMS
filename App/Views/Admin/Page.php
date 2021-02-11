<?php
namespace App\Views\Admin;

use App\Views\View;
use PAVApp\Core\ResultInterface;
use App\Config\Settings as AppSets;

class Page extends View
{
    public function generate(array $data = []): ResultInterface
    {
        $pageName = $data['page'];
        unset($data['page']);
        if (isset($data['subPage'])) {
            if ($data['subPage'] !== '') {
                $pageName .= sprintf('/%s', $data['subPage']);
            }
            unset($data['subPage']);
        }

        // generate view content
        $path = sprintf(
            "%s%spages/admin/%s/template.html",
            $_SERVER['DOCUMENT_ROOT'],
            AppSets::ROOT_DIR,
            $pageName
        );
        $viewContent = $this->Template->make($path, $data);

        // generate page and return
        $pageTemplate = $this->getPageTemplate($pageName);
        $path = sprintf(
            "%s%stemplates/admin/%s.html",
            $_SERVER['DOCUMENT_ROOT'],
            AppSets::ROOT_DIR,
            $pageTemplate
        );
        
        $pageContent = $this->Template->make(
            $path, [
            'head' => [
                'title' => 'kadmin',
                'rootDir' => AppSets::ROOT_DIR,
                'fileCacheFlag' => 3
            ],
            'header' => [
                'rootDir' => AppSets::ROOT_DIR,
            ],
            'js' => [
                'pageName' => $pageName,
                'rootDir' => AppSets::ROOT_DIR,
                'editTime' => date("Y-m-d H:i:s")
            ],
            'page' => [
                'content:raw' => $viewContent
            ]
        ]);

        $this->Result->setData([
            'output' => $pageContent
        ]);
        
        return $this->Result;
    }

    protected function getPageTemplate(string $pageName): string
    {
        $hasTemplates = ['auth', 'error'];
        return in_array($pageName, $hasTemplates) ? $pageName : "index";
    }
}