<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListProductCategories extends AJAXAction
{
    public function run(array $data = []): array
    {
        $categoryModel = $this->Site->model('DB\Category');

        $categories = $categoryModel->getList([
            'select' => ['category_id', 'category_name'],
            'where' => [
                'category_active' => 1
            ],
            'orderBy' => ['category_name' => 'desc']
        ]);

        $result = [];
        foreach ($categories as $item) {
            $result[$item['category_id']] = $item['category_name'];
        }

        return $result;
    }
}