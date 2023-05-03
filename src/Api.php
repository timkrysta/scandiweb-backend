<?php

namespace Timkrysta;

class Api {
    public static function exitIfHttpMethodNotIn(array $methods): void
    {
        if (in_array($_SERVER['REQUEST_METHOD'], $methods)) {
            return;
        }
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Method Not Allowed. This route supports only POST requests.'
        ]);
        exit();
    }
}