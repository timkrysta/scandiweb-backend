<?php

namespace Timkrysta;

class Api
{    
    /**
     * Exits when a request method is different than $supportedMethods.
     *
     * @param  string[] $supportedMethods
     * @return void
     */
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
