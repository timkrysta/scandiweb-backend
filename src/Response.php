<?php

namespace Timkrysta;

class Response {
    public static function json(mixed $data, int $response_code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($response_code);
        echo json_encode($data);
        exit();
    }
}