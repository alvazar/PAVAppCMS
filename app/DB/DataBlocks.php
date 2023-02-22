<?php
namespace App\DB;

/**
 * Класс для работы со страницами сайта.
 */
class DataBlocks extends DBModel
{
    /** Инициирует свойства модели
     * @return void
     */
    public function init(): void
    {
        // название поля ID
        $this->idName = 'ID';
        
        // название таблицы БД
        //$this->table = AppSets::PAGES_TABLE;
        $this->table = 'pavapp_data_blocks';

        // список полей для фильтрации списка
        $this->whereFields = [
            'ID' => 'is_numeric',
            'dataKey' => 'strip_tags',
            'dateCreate' => 'strip_tags',
            'dateModify' => 'strip_tags',
            'name' => 'strip_tags',
            'type' => 'strip_tags'
        ];

        // список полей для сохранения элемента
        $this->saveFields = [
            'dataKey' => 'strip_tags',
            'dateModify' => 'strip_tags',
            'name' => 'strip_tags',
            'type' => 'strip_tags',
            'data' => 'is_array'
        ];
    }

    public function install($drop = false): void
    {
        if ($drop) {
            $this->DB->query("DROP TABLE {$this->table}");
        }

        $qu = "CREATE TABLE IF NOT EXISTS {$this->table} (".
            "ID INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,".
            "dateCreate DATETIME DEFAULT CURRENT_TIMESTAMP,".
            "dateModify DATETIME DEFAULT CURRENT_TIMESTAMP,".
            "name VARCHAR(255) DEFAULT '',".
            "dataKey VARCHAR(20) DEFAULT '',".
            "type VARCHAR(20) DEFAULT '',".
            "data JSON DEFAULT NULL".
            ") ENGINE = InnoDB CHARACTER SET utf8";
        
        $this->DB->query($qu);
    }

    public function getList(array $params = []): array
    {
        $queryResult = $this->querySelect($params)->result();
        $result = [];

        while ($item = $queryResult->fetch()) {
            $item['data'] = (array) json_decode($item['data'] ?? '[]', true);
            
            $result[] = $item;
        }

        return $result;
    }
}
