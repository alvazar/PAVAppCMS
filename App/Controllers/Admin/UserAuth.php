<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use PAVApp\Core\ResultInterface;
use PAVApp\MVC\ModelInterface;

class UserAuth extends Controller
{
    protected function getParams(): array
    {
        return [
            'login' => $this->params['login'],
            'passw' => $this->params['passw'],
            'from' => $this->params['from'] ?? ""
        ];
    }

    protected function getModel(): ?ModelInterface
    {
        return new \App\Models\Admin\UserAuth();
    }

    public function actionDefault(): ResultInterface
    {
        $params = $this->getParams();
        $Result = $this->Model->apply($params);
        return $Result;
        /*if ($this->_Model->apply($params)) {
            Route::redirect($params['from']);
        }
        Route::redirect("error",[
            'code' => "error auth"
        ]);*/
    }
}