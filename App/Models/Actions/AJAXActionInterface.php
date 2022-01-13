<?php
namespace App\Models\Actions;

interface AJAXActionInterface
{
    public function run(array $data = []): array;
}