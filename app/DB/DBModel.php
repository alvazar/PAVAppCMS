<?php
namespace App\DB;

use PAVApp\MVC\DBModelAbstract;
use App\AppUnitInterface;
use App\AppUnitTrait;

abstract class DBModel extends DBModelAbstract implements AppUnitInterface
{
    use AppUnitTrait;
}
