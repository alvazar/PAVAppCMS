<?php
namespace App;

use Throwable;
use App\Dataset\AppData;

class AppHandler implements AppHandlerInterface
{
    private AppData $appData;
    private $movedClasses = [];

    /**
     * @param AppData $appData
     */
    public function __construct(AppData $appData)
    {
        $this->appData = $appData;
        
        // Список перемещённых классов в рамках рефакторинга.
        if (file_exists(__DIR__.'/movedClasses.php')) {
            $this->movedClasses = (array) require_once __DIR__ . '/movedClasses.php';
        }
    }

    /**
     * @param string $name
     * 
     * @return object|null
     */
    public function get(string $name): ?object
    {
        $name = ucfirst($name);
        
        // Проверяем был ли класс перемещён.
        $name = $this->movedClasses[$name] ?? $name;
        $name = sprintf('App\\%s', $name);
        
        try {
            return (new $name())->appUnitInit(['app' => $this]);
        } catch (Throwable $err) {
            return null;
        }
    }

    /**
     * deprecated
     * 
     * @param string $name
     * 
     * @return object|null
     */
    public function model(string $name): ?object
    {
        return $this->get($name);
    }

    public function data(): AppData
    {
        return $this->appData;
    }
}
