<?php
namespace App\DBModels;

use App\Config\Settings as AppSets;

class AdminData extends DBModel
{
    // название поля ID
    protected $idName = 'ID';
    
    // название таблицы БД
    protected $table = AppSets::DATA_TABLE;

    // поля для фильтрации списка
    protected $whereFields = [
        'ID' => 'regexp/[a-z\_0-9]+/i'
    ];

    // поля для сохранения элемента
    protected $saveFields = [
        'ID' => 'regexp/[a-z\_0-9]+/i',
        'data' => true
    ];
}