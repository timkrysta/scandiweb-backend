<?php

namespace Timkrysta;

class Response
{
    public const DEFAULT_CORS_ORIGIN = 'http://localhost:3000';
    
    /**
     * Return JSON response and exit
     *
     * @param  mixed $data
     * @param  int $response_code
     * @return void
     */
    public static function json(mixed $data, int $response_code = 200): void
    {
        self::allowCorsRequests();
        header('Content-Type: application/json');
        http_response_code($response_code);
        echo json_encode($data);
        exit();
    }
    
    /**
     * Return Validation Failed JSON response and exit
     *
     * @param  array $errors
     * @return void
     */
    public static function validationFailed(array $errors): void
    {
        self::json([
            'message' => 'Validation Failed',
            'error' => $errors,
        ], 422);
    }
    
    /**
     * Allow Cross-origin resource sharing (CORS) request
     *
     * @param  string $origin
     * @return void
     */
    public static function allowCorsRequests(string $origin = ''): void
    {
        $origin = $origin ?: self::DEFAULT_CORS_ORIGIN;
        header("Access-Control-Allow-Origin: {$origin}");
    }
}
