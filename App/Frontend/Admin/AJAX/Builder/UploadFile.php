<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class UploadFile extends AJAXAction
{
    protected const ERROR_UPLOAD = 'Ошибка загрузки файла';

    public function run(array $data = []): array
    {
        $uploadDir = !empty($data['uploadDir'])
            ? $data['uploadDir']
            : $this->makeUploadDirPath();

        $image = $this->app->get('Tools\Files\UploadFile');
        $imagePath = $image->load([
            'varName' => 'file',
            'dir' => $uploadDir,
            'rewriteFile' => false,
            'makeDirs' => true,
            'allowTypes' => ['pdf', 'mp4']
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
