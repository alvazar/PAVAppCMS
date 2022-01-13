<?php
namespace App\Models\Actions;

use App\Models\Dataset\AJAXResponseInterface;
use App\Models\Model;
use Throwable;

class AJAXActionHandler extends Model
{
    public function run(string $cl, array $data = []): AJAXResponseInterface
    {
        $AJAXResponse = $this->Site->model('Dataset\AJAXResponse');
        
        try {
            $responseData = $this->Site->model($cl)->run($data);
            $AJAXResponse->setData($responseData)->setSuccess();
        } catch(Throwable $err) {
            $AJAXResponse->setError($err->getMessage());
        }

        return $AJAXResponse;
    }
}