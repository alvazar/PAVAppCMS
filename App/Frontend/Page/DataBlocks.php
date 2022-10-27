<?php
namespace App\Frontend\Page;

use App\AppUnit;
use App\Frontend\Page\DataBlocks\DataBlockInterface;

/*
Класс для работы с блоками данных
 */

class DataBlocks extends AppUnit
{
    public function getList(array $params = []): array
    {
        $result = [];

        $dataBlocks = $this->Site->model('Frontend\Page\DataBlocks');
        
        $fnGetList = function (
            string $path,
            string $cutPath
        ) use (&$fnGetList, $dataBlocks): array {
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
                        $TemplObj = $dataBlocks->getByName($className);
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
            '%s/app/Frontend/Page/DataBlocks',
            $_SERVER['DOCUMENT_ROOT']
        );
        $result = $fnGetList($rootDir, $rootDir.'/');

        return $result;
    }

    public function getByName(string $name = ''): ?DataBlockInterface
    {
        return $this->Site->model(sprintf('Frontend\Page\DataBlocks\%s', $name));
    }
}