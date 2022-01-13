<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class UploadFile extends AJAXAction
{
    public function run(array $data = []): array
    {
        if (!empty($data['uploadDir'])) {
            $uploadDir = $data['uploadDir'];
        } else {
            $uploadDir = "/upload_2/page";
            for ($i = 0; $i < 3; $i++) {
                $uploadDir .= sprintf('/%s', mt_rand(0, 1000));
            }
        }

        $Image = $this->Site->model('Tools\Files\UploadFile');
        $imagePath = $Image->load([
            'varName' => 'file',
            'dir' => $uploadDir,
            'rewriteFile' => false,
            'makeDirs' => true,
            'allowTypes' => ['pdf', 'mp4']
        ]);

        $result = [];
        if ($imagePath !== '') {
            $result['path'] = $imagePath;
        } else {
            throw new \Exception('Ошибка загрузки файла');
        }

        return $result;
    }
}