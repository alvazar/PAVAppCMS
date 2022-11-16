<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class UploadImage extends AJAXAction
{
    protected const ERROR_UPLOAD = 'Ошибка загрузки изображения';
    
    protected const ALLOW_TYPES = ['jpg', 'png', 'gif', 'svg'];

    public function run(array $data = []): array
    {
        if (!empty($data['uploadDir'])) {
            $uploadImageDir = $data['uploadDir'];
        } elseif (!empty($data['oldImage'])) {
            $uploadImageDir = dirname($data['oldImage']);
        } else {
            $uploadImageDir = $this->makeUploadDirPath();
        }

        $uploadFile = $this->app->get('Tools\Files\UploadFile');
        $imagePath = $uploadFile->load([
            'varName' => 'file',
            'dir' => $uploadImageDir,
            'rewriteFile' => false,
            'makeDirs' => true,
            'allowTypes' => self::ALLOW_TYPES
        ]);
        
        if ($imagePath === '') {
            throw new \Exception(self::ERROR_UPLOAD);
        }

        return [
            'path' => $imagePath
        ];
    }

    protected function makeUploadDirPath(): string
    {
        $uploadDir = "/upload/page";

        for ($i = 0; $i < 3; $i++) {
            $uploadDir .= sprintf('/%s', mt_rand(0, 1000));
        }

        return $uploadDir;
    }
}
