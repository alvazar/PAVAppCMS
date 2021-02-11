<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Core\ResultInterface;
use PAVApp\MVC\Route;
use App\Config\Settings as AppSets;

class GetFullData extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        $data = Route::run('App\Controllers\Admin\GetData.actionDefault')->getData();
        $dirs = [];

        $listValues = [];
        if (!empty($data['listValues'])) {
            foreach ($data['listValues'] as $item) {
                if (!isset($listValues[$item['name']])) {
                    $listValues[$item['name']] = [];
                }
                if (!empty($item['variants'])) {
                    $lines = explode("\n", $item['variants']);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        list($key,$value) = explode(" ", $line,2);
                        $key = trim($key);
                        $value = trim($value);
                        $listValues[$item['name']][$key] = $value;
                    }
                }
                if (!empty($item['path'])) {
                    $dirs[$item['name']] = AppSets::ROOT_DIR.$item['path'];
                }
            }
        }

        foreach ($dirs as $listName => $path) {
            $listValues[$listName] = [];
            if (!file_exists($_SERVER['DOCUMENT_ROOT'].$path)) {
                continue;
            }
            $Dir = dir($_SERVER['DOCUMENT_ROOT'].$path);
            if ($Dir === false) {
                continue;
            }
            while($item = $Dir->read()) {
                if ($item !== "." && $item !== "..") {
                    $fullPath = $path.$item;
                    if (!is_dir($_SERVER['DOCUMENT_ROOT'].$fullPath)) {
                        $shortPath = str_replace(AppSets::ROOT_DIR, "", $fullPath);
                        $listValues[$listName][$shortPath] = $fullPath;
                    }
                }
            }
        }

        $this->Result->setData([
            'data' => $data,
            'listValues' => $listValues
        ]);

        return $this->Result;
    }
}