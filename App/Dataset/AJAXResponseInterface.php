<?php
namespace App\Dataset;

interface AJAXResponseInterface
{
    public function getResponse(): array;

    public function getResponseJSON(): string;
}
