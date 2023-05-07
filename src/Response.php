<?php

namespace Timkrysta;

class Response {
    public const DEFAULT_CORS_ORIGIN = 'http://localhost:3000';

    public static function json(mixed $data, int $response_code = 200): void
    {
        self::allowCorsRequests(self::DEFAULT_CORS_ORIGIN);
        header('Content-Type: application/json');
        http_response_code($response_code);
        echo json_encode($data);
        exit();
    }

    public static function validationFailed(array $errors): void
    {
        self::json([
            'message' => 'Validation Failed',
            'error' => $errors,
        ], 422);
    }

    public static function allowCorsRequests($origin)
    {
        header("Access-Control-Allow-Origin: {$origin}");
        #header("Access-Control-Allow-Methods: GET, POST");
        #header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }
}