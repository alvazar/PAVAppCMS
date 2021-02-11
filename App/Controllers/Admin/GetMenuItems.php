<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use PAVApp\Core\ResultInterface;
use PAVApp\MVC\ModelInterface;

class GetMenuItems extends Controller
{
    protected function getModel(): ?ModelInterface
    {
        return new \App\Models\Admin\GetData();
    }

    public function actionDefault(): ResultInterface
    {
        $Result = $this->Model->apply();
        $data = $Result->getData();
        $Result->setData(['result' => $data['adminMenu'] ?? []]);
        return $Result;
    }
}