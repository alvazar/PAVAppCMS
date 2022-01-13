<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListTrainers extends AJAXAction
{
    public function run(array $data = []): array
    {
        $result = [];
        
        $queryResult = $this->Site->model('DB\Trainer')->querySelect([
            'select' => ['trainer_id', 'trainer_name'],
            'where' => ['trainer_active' => 1]
        ])->result();
        
        while($item = $queryResult->fetch_assoc()) {
            $result[$item['trainer_id']] = $item['trainer_name'];
        }

        return $result;
    }
}
