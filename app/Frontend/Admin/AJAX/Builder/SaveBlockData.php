<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class SaveBlockData extends AJAXAction
{
    public function run(array $data = []): array
    {
        $ID = (int) ($data['ID'] ?? 0);
        $data = !empty($data['data']) ? $data['data'] : [];
        $skipParams = ['ID', 'site_id', 'dateCreate', 'dateModify'];

        foreach ($data as $key => $val) {

            if (in_array($key, $skipParams)) {
                unset($data[$key]);
            }
        }

        /*
        [FIX] добавляем данные по умолчанию, 
        т.к. через ajax пустые объекты не передаются
        */
        $data += ['data' => []];
        $data['dateModify'] = date('Y-m-d H:i:s');
        
        //
        $pagesModel = $this->app->get('DB\DataBlocks');
        $saveParams = [
            'fields' => $data
        ];
        
        if ($ID > 0) {
            $saveParams['where'] = ['ID' => $ID];
            $currPage = $pagesModel->getByID($ID);
        }

        $result = [];

        if ($isSaved = $pagesModel->save($saveParams)) {
            $result['newID'] = $pagesModel->getInsertID();
        }

        // log changing active page
        /*
        if (
            $isSaved
            && (
                (!empty($currPage) && (int) $currPage['active'] !== (int) $data['active'])
                || !empty($result['newID'])
            )
        ) {
            $pageID = $ID > 0 ? $ID : $result['newID'];
            $this->Site->model('DB\Log')->save([
                'fields' => [
                    'type' => sprintf('builderChangePageActive_%d', $pageID),
                    'info' => [
                        'IP' => $_SERVER['REMOTE_ADDR'],
                        'date' => date('Y-m-d H:i:s'),
                        'pageID' => $pageID,
                        'active' => $data['active'],
                        'saveActive' => $data['active'],
                        'currActive' => $currPage['active'] ?? '',
                        'newID' => $result['newID'] ?? ''
                    ]
                ]
            ]);
        }
        */

        return $result;
    }
}
