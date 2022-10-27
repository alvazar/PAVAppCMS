<?php
namespace App\Frontend\Admin\AJAX\Builder;

use App\Actions\AJAXAction;

class ListFromField extends AJAXAction
{
    public function run(array $data = []): array
    {
        $result = [];

        if (
            !empty($data['pageID'])
            && !empty($data['fieldName'])
        ) {
            $pageData = $this->Site->model('DB\Pages')->getByID($data['pageID']);
            
            $lst = $this->getValueFromString($data['fieldName'], $pageData);

            foreach ($lst as $key => $item) {
                $keyName = (
                    !empty($data['bindKey'])
                    && is_array($item)
                    && array_key_exists($data['bindKey'], $item)
                ) ? $item[$data['bindKey']] : $key;
                
                $result[$keyName] = (
                    !empty($data['titleKey'])
                    && is_array($item)
                    && array_key_exists($data['titleKey'], $item)
                ) ? $item[$data['titleKey']] : $item;
            }
        }

        return $result;
    }

    protected function getValueFromString(string $fieldName, array $data): array
    {
        $fieldName = str_replace(']', '', $fieldName);
        $keys = explode('[', $fieldName);

        $fn = function ($step, $keys, $data) use (&$fn): array {
            $result = [];
        
            if (array_key_exists($keys[$step], $data)) {
                $result = (array) $data[$keys[$step]];
                $step++;
                if (isset($keys[$step]) && isset($result[$keys[$step]])) {
                    $result = $fn($step, $keys, $result);
                }
            }
        
            return (array) $result;
            
        };

        return $fn(0, $keys, $data);
    }
}