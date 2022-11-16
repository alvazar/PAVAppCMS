<?php
namespace App;

trait AppUnitTrait
{
    protected $Site; // deprecated
    protected $app;

    public function appUnitInit(array $params = []): self
    {
        if (!empty($params['app'])) {
            $this->Site = $params['app'];
            $this->app = $params['app'];
        }

        $this->afterAppUnitInit();
        
        return $this;
    }

    public function afterAppUnitInit(): void
    {
    }
}
