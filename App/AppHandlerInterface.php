<?php
namespace App;

use App\Dataset\AppData;

interface AppHandlerInterface
{
    public function __construct(AppData $SiteData);
    public function get(string $name): ?object;
}