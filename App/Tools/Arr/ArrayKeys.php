<?php
namespace App\Tools\Arr;

use App\AppUnit;

/*
Класс преобразует ключи массива
 */

class ArrayKeys extends AppUnit
{
    /** Рекурсивно удаляет заданный префикс у ключей массива
     * @param array $data
     * @param string $prefix
     * 
     * @return array
     */
    public function removePrefix(array $data, string $prefix): array
    {
        $result = [];

        foreach ($data as $key => $value) {

            if (is_string($key)) {
                $key = preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $key);
            }

            $result[$key] = is_array($value)
                ? $this->removePrefix($value, $prefix)
                : $value;
        }

        return $result;
    }

    /** Добавляет префикс ключам массива
     * @param array $data
     * @param string $prefix
     * 
     * @return array
     */
    public function addPrefix(array $data, string $prefix): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[$prefix . $key] = $value;
        }

        return $result;
    }
}