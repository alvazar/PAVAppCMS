<?php
namespace App\Actions;

interface AJAXActionInterface
{
    public function run(array $data = []): array;
}
