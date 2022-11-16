<?php
namespace App\Actions;

use App\AppUnit;

abstract class AJAXAction extends AppUnit implements AJAXActionInterface
{
    abstract public function run(array $data = []): array;
}
