<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use PAVApp\Core\ResultInterface;
use PAVApp\Core\Result;
use PAVApp\MVC\Route;

class PostActionsHandler extends Controller
{
    // override
    public function actionDefault(): ResultInterface
    {
        $response = [
            "type" => "error"
        ];
        
        $action = $this->params['action'] ?? '';
        $params = [];
        $params['data'] = $this->params['data'] ?? [];
        
        // process
        if ($action === "getData") {
            $action = "getFullData";
        } elseif ($action === "edit") {
            $action = "editData";
        }
        
        if ($action !== "") {
            try {
                $response['type'] = "success";
                $actionResult = Route::run(
                    sprintf('App\Controllers\Admin\%s.actionDefault', $action),
                    $params
                )->getData();
                if (!empty($actionResult)) {
                    $response += $actionResult;
                }
            } catch (\Throwable $Err) {
                $response['result'] = $Err->getMessage();
            }
        }
        
        $Result = new Result();
        $Result->setData([
            'output' => json_encode($response)
        ]);

        return $Result;
    }
}