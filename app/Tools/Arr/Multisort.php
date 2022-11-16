<?php
namespace App\Tools\Arr;

use App\AppUnit;

/*
Класс сортирует многомерные массивы, 
с возможностью сортировать по нескольким параметрам.
 */

class Multisort extends AppUnit
{
    /**
     * Сортирует многомерный массив по нескольким параметрам
     * Например run($data, [orderBy => ['name' => 'asc', 'ID' => 'desc'])
     * @param array $data - Сортируемый массив.
     * @param array $params - Параметры сортировки.
     * 
     * @return array
     */
    public function run(array $data, array $params = []): array
    {
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

        if (!is_array($result)) {
            return [];
        }

        if ($indexKey !== null) {
            $prepared = [];

            foreach ($result as $item) {
                $prepared[$item[$indexKey]] = $item;
            }

            $result = $prepared;
        }

        return $result;
    }
}
