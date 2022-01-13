<?php
namespace App\Models\Dataset;

interface AJAXResponseInterface
{
    public function getResponse(): array;
    public function getResponseJSON(): string;
}