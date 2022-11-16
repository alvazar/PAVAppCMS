<?php
namespace App\Actions;

use App\AppUnit;
use App\Dataset\AJAXResponseInterface;
use Throwable;

class AJAXActionHandler extends AppUnit
{
    public function run(string $cl, array $data = []): AJAXResponseInterface
    {
        $AJAXResponse = $this->app->get('Dataset\AJAXResponse');
        
        try {
            $responseData = $this->app->get($cl)->run($data);
            $AJAXResponse->setData($responseData)->setSuccess();
        } catch(Throwable $err) {
            $AJAXResponse->setError($err->getMessage());
        }

        return $AJAXResponse;
    }
}
