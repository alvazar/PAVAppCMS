<?php
namespace App\Tools\Files;

use App\AppUnit;

/**
 * Класс для загрузки файлов на сервер.
 */
class UploadFile extends AppUnit
{
    /**
     * Загружает файл
     * @param array $params
     * 
     * @return string
     */
    public function load(array $params = []): string
    {
        // prepare params
        $varName = $params['varName'] ?? '';
        $newName = $params['newName'] ?? '';
        $rewriteFile = $params['rewriteFile'] ?? true;
        $dir = $params['dir'] ?? '';
        if ($dir[-1] !== '/') {
            $dir .= '/';
        }
        $dir = $_SERVER['DOCUMENT_ROOT'].$dir;

        if (!empty($params['makeDirs']) && !file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        //
        if ($_FILES[$varName]['error'] != UPLOAD_ERR_OK) {
            return '';
        }
        $fileName = $_FILES[$varName]['name'];
        $tmpPath = $_FILES[$varName]['tmp_name'];

        // settings
        $allowTypes = (array) ($params['allowTypes'] ?? []);
        
        // prepare and check file extension
        $fileType = $this->getExtension($fileName);
        if (!in_array($fileType, $allowTypes)) {
            return '';
        }

        // local func for gener new name
        $getNewName = function ($name, $type, $dir, $step = 1) use(&$getNewName) {
            $newName = sprintf('%s_%d.%s', $name, $step, $type);
            return file_exists($dir.$newName)
                ? $getNewName($name, $type, $dir, $step + 1)
                : $newName;
        };

        //
        $fileName = $newName !== '' ? $newName.'.'.$fileType : $fileName;
        $filePath = sprintf('%s%s', $dir, $fileName);
        if (!$rewriteFile && file_exists($filePath)) {
            $baseName = mb_substr($fileName, 0, mb_strrpos($fileName, '.'));
            $filePath = $dir.$getNewName($baseName, $fileType, $dir);
        }
        $isUploaded = move_uploaded_file($tmpPath, $filePath);
        if (!$isUploaded) {
            return '';
        }
        chmod($filePath, 0644);

        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
    }

    /**
     * Возвращает расширение файла
     * @param string $fileName
     * 
     * @return string
     */
    public function getExtension(string $fileName):string {
        $fileType = mb_substr($fileName, mb_strrpos($fileName, '.') + 1);
        $fileType = mb_strtolower($fileType);
        $fileType = str_replace('jpeg', 'jpg', $fileType);
        return $fileType;
    }
}