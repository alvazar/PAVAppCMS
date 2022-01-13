<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class UploadImage extends AJAXAction
{
    public function run(array $data = []): array
    {
        if (!empty($data['uploadDir'])) {
            $uploadImageDir = $data['uploadDir'];
        } elseif (!empty($data['oldImage'])) {
            $uploadImageDir = dirname($data['oldImage']);
        } else {
            $uploadImageDir = "/upload_2/page";
            for ($i = 0; $i < 3; $i++) {
                $uploadImageDir .= sprintf('/%s', mt_rand(0, 1000));
            }
        }

        $uploadFile = $this->Site->model('Tools\Files\UploadFile');
        $imagePath = $uploadFile->load([
            'varName' => 'file',
            'dir' => $uploadImageDir,
            'rewriteFile' => false,
            'makeDirs' => true,
            'allowTypes' => ['jpg', 'png', 'gif', 'svg']
        ]);

        $result = [];
        if ($imagePath !== '') {
            $result['path'] = $imagePath;
        } else {
            throw new \Exception('Ошибка загрузки изображения');
        }

        return $result;
    }
}