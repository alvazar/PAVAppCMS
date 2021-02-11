<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Storage\DBStorage;
use PAVApp\Core\ResultInterface;
use App\Config\Settings as AppSets;

class GetData extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        //
        /*$MC = MCSingleton::getInstance();

        // get data
        $cacheID = "k_data_".KADMIN_PROJECT_ID;
        $result = $MC->get($cacheID);
        if ($result !== false) {
            return $result;
        }*/

        //
        $DB = DBStorage::getInstance();

        $St = $DB->prepare("SELECT data, editTime FROM ".AppSets::DATA_TABLE." WHERE ID = :projectID LIMIT 1");
        $St->execute([
            'projectID' => AppSets::PROJECT_ID
        ]);
        $St->setFetchMode(\PDO::FETCH_ASSOC);
        $res = $St->fetch();
        $resultData = !empty($res['data']) ? json_decode(base64_decode($res['data']), true) : [];
        if (!empty($resultData)) {
            $resultData['editTime'] = $res['editTime'];
        }
        //$MC->set($cacheID,$result,0,60);

        $this->Result->setData($resultData);

        return $this->Result;
    }
}