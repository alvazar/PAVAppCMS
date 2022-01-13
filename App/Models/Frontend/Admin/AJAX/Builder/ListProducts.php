<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListProducts extends AJAXAction
{
    public function run(array $data = []): array
    {
        $productModel = $this->Site->model('DB\Product');

        $products = $productModel->getList([
            'select' => ['product_id', 'product_name'],
            'where' => [
                //'product_active' => 1
            ],
            'orderBy' => ['product_id' => 'asc']
        ]);

        $result = [];
        foreach ($products as $item) {
            $result[$item['product_id']] = sprintf(
                '[%d] %s',
                $item['product_id'],
                $item['product_name']
            );
        }

        return $result;
    }
}