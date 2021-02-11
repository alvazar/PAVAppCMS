<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Storage\DBStorage;
use PAVApp\Core\ResultInterface;

class UserCheckExists extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        $login = $params['login'] ?? '';

        $DB = DBStorage::getInstance();
        $St = $DB->prepare("SELECT ID FROM kadmin_users WHERE login = :login LIMIT 1");
        $St->execute([
            'login' => $login
        ]);
        
        $this->Result->setData([
            'userExists' => $St->rowCount() > 0
        ]);

        return $this->Result;
    }
}