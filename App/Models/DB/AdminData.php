<?php
namespace App\DB\Models;

use App\Config\Settings as AppSets;

class AdminData extends DBModel
{
    public function init()
    {
        // название поля ID
        $this->idName = 'ID';
        
        // название таблицы БД
        $this->table = AppSets::DATA_TABLE;

        // поля для фильтрации списка
        $this->whereFields = [
            'ID' => 'regexp/[a-z\_0-9]+/i'
        ];

        // поля для сохранения элемента
        $this->saveFields = [
            'ID' => 'regexp/[a-z\_0-9]+/i',
            'data' => true
        ];
    }
}