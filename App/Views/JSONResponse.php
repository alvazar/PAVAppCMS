<?php
namespace App\Views;

use PAVApp\MVC\ViewAbstract;
use PAVApp\core\ResultInterface;

class JSONResponse extends ViewAbstract
{
    public function generate(array $data = []): ResultInterface
    {
        $response = [
            "type" => "success"
        ] + $data;
        $this->Result->setData([
            'output' => json_encode($response)
        ]);
        return $this->Result;
    }
}