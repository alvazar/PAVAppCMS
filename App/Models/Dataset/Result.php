<?php
namespace App\Models\Dataset;

use App\Models\Model;

class Result extends Model
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