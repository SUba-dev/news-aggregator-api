<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    protected $statusCode;

    public function __construct($message, $statusCode = 400)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function render()
    {
        return response()->json([
            'error' => true,
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}
