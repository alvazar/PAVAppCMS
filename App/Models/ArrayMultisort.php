<?php
namespace App\Models;

use PAVApp\Core\ResultInterface;

/*
Класс сортирует многомерные массивы, 
с возможностью сортировать по нескольким параметрам.
 */

class ArrayMultisort extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        $params = $params['params'] ?? [];
        $data = $params['data'] ?? [];

        $orderBy = $params['orderBy'] ?? [];
        $indexKey = $params['indexKey'] ?? null;

        $args = [];
        foreach ($orderBy as $key => $direction) {
            $args[] = array_column($data, $key);
            $args[] = mb_strtolower($direction) === 'asc' ? SORT_ASC : SORT_DESC;
        }
        $args[] = $data;

        array_multisort(...$args);
        $result = array_pop($args);

        if (is_array($result)) {
            if ($indexKey !== null) {
                $prepared = [];
                foreach ($result as $item) {
                    $prepared[$item[$indexKey]] = $item;
                }
                $result = $prepared;
            }
        } else {
            $result = [];
        }

        $this->Result->setData($result);

        return $this->Result;
    }
}