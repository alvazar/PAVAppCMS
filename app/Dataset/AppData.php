<?php
namespace App\Dataset;

/**
 */
final class AppData
{
    public function __construct()
    {
    }

    /**
     * @param string $name
     * 
     * @return mixed
     */
    public function __get(string $name)
    {
        return property_exists($this, $name) === true ? $this->$name : null;
    }

    public function set(string $name, $value): self
    {
        $this->$name = $value;
        
        return $this;
    }

    public function add(string $name, $value): self
    {
        if (!isset($this->$name)) {
            $this->$name = [];
        }

        if (is_array($this->$name)) {
            $this->$name[] = $value;
        } elseif (is_string($this->$name)) {
            $this->$name .= $value;
        }

        return $this;
    }

    public function get(string $name)
    {
        return $this->$name ?? null;
    }
}
