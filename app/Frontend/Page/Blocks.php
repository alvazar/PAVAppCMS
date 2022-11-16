<?php
namespace App\Frontend\Page;

use App\AppUnit;
use App\Frontend\Page\Blocks\BlockInterface;

/*
Класс для работы с блоками страницы
 */

class Blocks extends AppUnit
{
    public function getList(array $params = []): array
    {
        $rootDir = sprintf(
            '%s/app/Frontend/Page/Blocks',
            $_SERVER['DOCUMENT_ROOT']
        );

        return $this->getListRecursive($rootDir, $rootDir . '/');
    }

    public function getByName(string $name = ''): ?BlockInterface
    {
        return $this->app->get(sprintf('Frontend\Page\Blocks\%s', $name));
    }

    protected function getListRecursive(string $path, string $cutPath): array
    {
        $result = [];
        $pageBlocks = $this->app->get('Frontend\Page\Blocks');
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
            $blockObj = $pageBlocks->getByName($className);

            if (!is_object($blockObj)) {
                continue;
            }

            $blockMeta = $blockObj->meta();
            $name = $blockMeta->name();

            if (empty($name)) {
                $name = $className;
            }

            $result[$className] = $name;
        }

        $dir->close();

        return $result;
    }
}
