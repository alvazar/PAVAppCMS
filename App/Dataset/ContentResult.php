<?php
namespace App\Dataset;

use App\AppUnit;

class ContentResult extends AppUnit implements ContentResultInterface
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