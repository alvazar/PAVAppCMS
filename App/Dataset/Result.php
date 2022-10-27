<?php
namespace App\Dataset;

use App\AppUnit;

class Result extends AppUnit
{
    protected $data = [];

    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->data;
    }
}