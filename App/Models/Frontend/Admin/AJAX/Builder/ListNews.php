<?php
namespace App\Models\Frontend\Admin\AJAX\Builder;

use App\Models\Actions\AJAXAction;

class ListNews extends AJAXAction
{
    public function run(array $data = []): array
    {
        $newsModel = $this->Site->model('DB\News');

        $news = $newsModel->getList([
            'select' => ['news_id', 'news_name'],
            'where' => [
            ],
            'orderBy' => ['news_id' => 'asc']
        ]);

        $result = [];
        foreach ($news as $item) {
            $result[$item['news_id']] = sprintf(
                '[%d] %s',
                $item['news_id'],
                $item['news_name']
            );
        }

        return $result;
    }
}