<?php
namespace App\Models\DB;

use App\Config\Settings as AppSets;

/**
 * Класс для работы со страницами сайта.
 */
class Pages extends DBModel
{
    /** Инициирует свойства модели
     * @return void
     */
    public function init(): void
    {
        // название поля ID
        $this->idName = 'ID';
        
        // название таблицы БД
        $this->table = AppSets::PAGES_TABLE;

        // список полей для фильтрации списка
        $this->whereFields = [
            'ID' => 'is_numeric',
            'site_id' => 'is_numeric',
            'active' => 'is_numeric',
            'section' => ['is_string', 'strip_tags'],
            'url' => 'strip_tags',
            'dateCreate' => 'strip_tags',
            'dateModify' => 'strip_tags',
            'sort' => 'is_numeric',
            'template' => 'strip_tags'
        ];

        // список полей для сохранения элемента
        $this->saveFields = [
            'site_id' => 'is_numeric',
            'url' => 'strip_tags',
            'active' => 'is_numeric',
            'section' => ['is_string', 'strip_tags'],
            'dateModify' => 'strip_tags',
            'meta' => 'is_array',
            'sort' => 'is_numeric',
            'template' => 'strip_tags',
            'templateData' => 'is_array',
            'blocks' => 'is_array',
        ];
    }

    public function install($drop = false): void
    {
        if ($drop) {
            $this->db->query("DROP TABLE {$this->table}");
        }

        $qu = "CREATE TABLE IF NOT EXISTS {$this->table} (".
            "ID INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,".
            "dateCreate DATETIME DEFAULT CURRENT_TIMESTAMP,".
            "dateModify DATETIME DEFAULT CURRENT_TIMESTAMP,".
            "active INT(1) DEFAULT 0,".
            "meta JSON DEFAULT NULL,".
            "url VARCHAR(255) DEFAULT '',".
            "section VARCHAR(20) DEFAULT '',".
            "template VARCHAR(20) DEFAULT '',".
            "templateData JSON DEFAULT NULL,".
            "blocks JSON DEFAULT NULL,".
            "site_id INT(2) NOT NULL,".
            "sort INT(10) DEFAULT 0".
            ") ENGINE = InnoDB CHARACTER SET utf8";
        
        $this->db->query($qu);
    }

    public function getList(array $params = []): array
    {
        $queryResult = $this->querySelect($params)->result();
        $result = [];
        $pageHash = $this->Site->model('Info\PageHash');

        while ($item = $queryResult->fetch_assoc()) {
            $item['templateData'] = (array) json_decode($item['templateData'] ?? '[]', true);
            $item['blocks'] = (array) json_decode($item['blocks'] ?? '[]', true);
            $item['meta'] = (array) json_decode($item['meta'] ?? '[]', true);

            $item['urlWithHash'] = '';
            if (!empty($item['url'])) {
                $url = sprintf(
                    '%s/%s',
                    $item['section'] ?? '',
                    $item['url'] ?? ''
                );
                if ($url[0] !== '/') {
                    $url = '/'.$url;
                }
                $item['urlFull'] = $url;
                $item['urlWithHash'] = sprintf(
                    '%s?pageHash=%s',
                    $url,
                    $pageHash->getHash($item['ID'])
                );
            }
            
            $result[] = $item;
        }

        return $result;
    }
}
