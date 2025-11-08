<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    protected $statusCode;
    protected $data;

    public function __construct($message = "", $statusCode = 400, $data = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    public function render($request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->message,
            'code' => $this->statusCode,
            'timestamp' => now()->toISOString(),
        ];

        if ($this->data) {
            $response['data'] = $this->data;
        }

        return response()->json($response, $this->statusCode);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}