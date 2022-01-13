<?php
namespace App\Models\Actions;

use App\Models\Model;

abstract class AJAXAction extends Model implements AJAXActionInterface
{
    abstract public function run(array $data = []): array;
}