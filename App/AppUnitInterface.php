<?php
namespace App;

interface AppUnitInterface
{
    public function appUnitInit(array $params = []): self;
    public function afterAppUnitInit(): void;
}
