<?php
namespace App\Models\Admin;

use App\Models\Model;
use PAVApp\Core\ResultInterface;

class UserCheckAuth extends Model
{
    public function apply(array $params = []): ResultInterface
    {
        $isAuth = !empty($_SESSION['kadmin_user']) && 
            !empty($_SESSION['kadmin_user']['ID']);
        $this->Result->setData([
            'isAuth' => $isAuth
        ]);
        
        return $this->Result;
    }
}