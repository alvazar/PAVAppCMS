<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListDataBlockItems extends AJAXAction
{
    public function run(array $data = []): array
    {
        $items = $this->app->get('DB\DataBlocks')->getList([
            'select' => ['ID', 'name'],
            'orderBy' => ['name' => 'asc']
        ]);
        $result = [];
        foreach ($items as $item) $result[$item['ID']] = $item['name'];

        return $result;
    }
}
