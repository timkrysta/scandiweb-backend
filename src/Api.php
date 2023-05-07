<?php

namespace Timkrysta;

class Api {
    public static function exitIfRequestMethodNotSupported(array $supportedMethods): void
    {
        if (in_array($_SERVER['REQUEST_METHOD'], $supportedMethods, true)) {
            return;
        }

        Response::json([
            'message' => 'Method Not Allowed. This route supports only ' . implode(', ', $supportedMethods) . '.'
        ], 405);
    }
}
