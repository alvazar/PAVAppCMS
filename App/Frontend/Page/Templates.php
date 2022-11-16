<?php
namespace App\Frontend\Page;

use App\AppUnit;
use App\Frontend\Page\Templates\TemplateInterface;

/*
Класс для работы с шаблонами страниц
 */

class Templates extends AppUnit
{
    public function getList(array $params = []): array
    {
        $result = [];

        $pageTemplates = $this->Site->model('Frontend\Page\Templates');
        
        $fnGetList = function (
            string $path,
            string $cutPath
        ) use (&$fnGetList, $pageTemplates): array {
            $result = [];
            $dir = dir($path);
            while ($item = $dir->read()) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $path.'/'.$item;
                    if (is_dir($itemPath)) {
                        $result = array_merge($result, $fnGetList($itemPath, $cutPath));
                    } else {
                        $className = str_replace($cutPath, '', $itemPath);
                        $className = str_replace('/', '\\', $className);
                        $className = str_replace('.php', '', $className);
                        $TemplObj = $pageTemplates->getByName($className);
                        if (is_object($TemplObj)) {
                            $TemplMeta = $TemplObj->meta();
                            $name = $TemplMeta->name();
                            if (empty($name)) {
                                $name = $className;
                            }
                            $result[$className] = $name;
                        }
                    }
                }
            }
            $dir->close();

            return $result;
        };

        $rootDir = sprintf(
            '%s/app/Frontend/Page/Templates',
            $_SERVER['DOCUMENT_ROOT']
        );
        $result = $fnGetList($rootDir, $rootDir.'/');

        return $result;
    }

    public function getByName(string $name = ''): ?TemplateInterface
    {
        return $this->app->get(sprintf('Frontend\Page\Templates\%s', $name));
    }

    public function getListRecursive(string $path, string $cutPath): array
    {
        $result = [];
        $pageTemplates = $this->app->get('Frontend\Page\Templates');
        $dir = dir($path);
        
        while ($item = $dir->read()) {

            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . '/' . $item;

            if (is_dir($itemPath)) {
                $result = array_merge(
                    $result,
                    $this->getListRecursive($itemPath, $cutPath)
                );
                continue;
            }

            $className = str_replace($cutPath, '', $itemPath);
            $className = str_replace('/', '\\', $className);
            $className = str_replace('.php', '', $className);
            $templObj = $pageTemplates->getByName($className);

            if (!is_object($templObj)) {
                continue;
            }

            $templMeta = $templObj->meta();
            $name = $templMeta->name();

            if (empty($name)) {
                $name = $className;
            }

            $result[$className] = $name;
        }

        $dir->close();

        return $result;
    }
}
