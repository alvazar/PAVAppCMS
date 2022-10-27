<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListImages extends AJAXAction
{
    public function run(array $data = []): array
    {
        $path = !empty($data['dir'])
            ? sprintf('%s%s', $_SERVER['DOCUMENT_ROOT'], $data['dir'])
            : '';

        $result = [];
        if ($path !== '' && file_exists($path)) {
            $DirObj = dir($path);
            while ($item = $DirObj->read()) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $data['dir'] . '/' . $item;
                    $result[$itemPath] = $itemPath;
                }
            }
            $DirObj->close();
        }

        // clear doubles images, if webp exists
        foreach ($result as $key => $item) {
            preg_match('/(.+)\.(.+)$/', $key, $match);
            if (
                $match[2] !== 'webp' 
                && array_key_exists($match[1] . '.webp', $result)
            ) {
                unset($result[$key]);
            }
        }

        // correct sort with cyrilic
        uasort($result, function ($val1, $val2) {
            return strnatcmp(mb_strtolower($val1), mb_strtolower($val2));
        });

        return $result;
    }
}