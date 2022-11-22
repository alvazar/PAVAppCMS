<?php
namespace App\Dataset;

use PAVApp\Traits\DataTrait;

/**
 */
final class AppData
{
    use DataTrait;

    /**
     * @param string $name
     * 
     * @return mixed
     */
    public function __get(string $name)
    {
        return property_exists($this, $name) === true ? $this->$name : null;
    }

    public function add(string $name, $value): self
    {
        if (!array_key_exists($name, $this->data)) {
            $this->data[$name] = [];
        }

        if (is_array($this->data[$name])) {
            $this->data[$name][] = $value;
        } elseif (is_string($this->data[$name])) {
            $this->data[$name] .= $value;
        }

        return $this;
    }
}
