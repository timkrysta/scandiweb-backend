<?php

namespace Timkrysta;

class Response {
    public static function json(mixed $data, int $response_code = 200): void
    {
        self::allowCorsRequests();
        header('Content-Type: application/json');
        http_response_code($response_code);
        echo json_encode($data);
        exit();
    }
    public static function allowCorsRequests()
    {
        // Allow cross-origin requests
        header("Access-Control-Allow-Origin: http://localhost:3000");
        #header("Access-Control-Allow-Methods: GET, POST");
        #header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }
}