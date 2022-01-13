<?php
namespace App\Models\Frontend\Page;

use App\Models\Frontend\Page\Templates\TemplateInterface;
use App\Models\Model;

/*
Класс для работы с шаблонами страниц
 */

class Templates extends Model
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
            '%s/app/Models/Frontend/Page/Templates',
            $_SERVER['DOCUMENT_ROOT']
        );
        $result = $fnGetList($rootDir, $rootDir.'/');

        return $result;
    }

    public function getByName(string $name = ''): ?TemplateInterface
    {
        return $this->Site->model(sprintf('Frontend\Page\Templates\%s', $name));
    }
}