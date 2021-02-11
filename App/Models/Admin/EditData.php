<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Storage\DBStorage;
use PAVApp\Core\ResultInterface;
//use app\core\MCSingleton;
use PAVApp\MVC\Route;
use App\Config\Settings as AppSets;

class EditData extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        // check exists current data
        $curData = Route::run('App\Controllers\Admin\GetData.actionDefault')->getData();
        $data = $params['data'] ?? false;
        if (empty($data)) {
            throw new \Exception("Данные не переданы");
        }

        //
        //$MC = MCSingleton::getInstance();
        
        // check edited time
        if (!empty($curData['editTime']) &&
            strtotime($data['editTime']) < strtotime($curData['editTime'])) {
            //throw new \Exception("Данные были отредактированы после того, как вы открыли страницу. Обновите страницу");
        }

        // create or update database record with konkurs data
        $sData = json_encode($data);
        if (empty($sData)) {
            throw new \Exception("Сериализация объекта невозможна");
        }
        $sData = base64_encode($sData);
        if (empty($sData)) {
            throw new \Exception("Кодирование объекта невозможно");
        }

        //
        $DB = DBStorage::getInstance();

        $sqlData = [
            'data' => $sData,
            'projectID' => AppSets::PROJECT_ID
        ];
        if (!empty($curData)) {
            $St = $DB->prepare("UPDATE ".AppSets::DATA_TABLE." SET data = :data,".
                "editTime = NOW() WHERE ID = :projectID LIMIT 1");
            $St->execute($sqlData);
        } else {
            $St = $DB->prepare("INSERT INTO ".AppSets::DATA_TABLE." VALUES (:projectID,:data,NOW())");
            $St->execute($sqlData);
        }

        // clear cache
        /*$cacheID = "k_data_".AppSets::PROJECT_ID;
        $MC->delete($cacheID);*/

        if ($St->errorCode() !== 0) {
            $this->Result->setError("DB query error");
        }
        
        return $this->Result;
    }
}