<?php
namespace App\Dataset;

use App\AppUnit;

class AJAXResponse extends AppUnit implements AJAXResponseInterface
{
    protected $data = [];
    protected $isSuccess = false;
    protected $message = '';

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getResponse(): array
    {
        return [
            'type' => $this->isSuccess ? 'success' : 'error',
            'data' => $this->data,
            'message' => $this->message
        ];
    }

    public function getResponseJSON(): string
    {
        return json_encode($this->getResponse());
    }

    public function setSuccess(string $message = ''): self
    {
        $this->message = $message;
        $this->isSuccess = true;

        return $this;
    }

    public function setError(string $message = ''): self
    {
        $this->message = $message;
        $this->isSuccess = false;

        return $this;
    }
}
