<?php
namespace App\Models\Frontend\Page;

use App\Models\Frontend\Page\Blocks\BlockInterface;
use App\Models\Model;

/*
Класс для работы с блоками страницы
 */

class Blocks extends Model
{
    public function getList(array $params = []): array
    {
        $result = [];

        $pageBlocks = $this->Site->model('Frontend\Page\Blocks');
        
        $fnGetList = function (
            string $path,
            string $cutPath
        ) use (&$fnGetList, $pageBlocks): array {
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
                        $BlockObj = $pageBlocks->getByName($className);
                        if (is_object($BlockObj)) {
                            $BlockMeta = $BlockObj->meta();
                            $name = $BlockMeta->name();
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
            '%s/app/Models/Frontend/Page/Blocks',
            $_SERVER['DOCUMENT_ROOT']
        );
        $result = $fnGetList($rootDir, $rootDir.'/');

        return $result;
    }

    public function getByName(string $name = ''): ?BlockInterface
    {
        return $this->Site->model(sprintf('Frontend\Page\Blocks\%s', $name));
    }
}