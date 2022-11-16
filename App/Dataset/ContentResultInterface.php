<?php
namespace App\Dataset;

interface ContentResultInterface
{
    public function content(?string $content = null): string;
}
