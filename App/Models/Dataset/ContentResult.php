<?php
namespace App\Models\Dataset;

use App\Models\Model;

class ContentResult extends Model implements ContentResultInterface
{
    protected $content = '';
    
    public function content(string $content = ''): string
    {
        if ($content !== '') {
            $this->content = $content;
        }

        return $this->content;
    }
}