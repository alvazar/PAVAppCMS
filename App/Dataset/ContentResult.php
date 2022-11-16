<?php
namespace App\Dataset;

use App\AppUnit;

class ContentResult extends AppUnit implements ContentResultInterface
{
    protected $content = '';
    
    public function content(?string $content = null): string
    {
        if (isset($content)) {
            $this->content = $content;
        }

        return $this->content;
    }
}
