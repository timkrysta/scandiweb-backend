<?php

use Timkrysta\Api;
use Timkrysta\Response;
use Timkrysta\Models\Book;
use Timkrysta\Models\DVD;
use Timkrysta\Models\Furniture;
use Timkrysta\ProductValidator;

require_once __DIR__ . '/../../vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['POST']);

$validator = ProductValidator::validateStoreRequest();

if ($validator->fails()) {
    Response::validationFailed($validator->errors);
}

$attributes = $validator->validated();

if (isset($attributes['weight'])) {
    $product = new Book($attributes);
} elseif (isset($attributes['size'])) {
    $product = new DVD($attributes);
} elseif (
    isset($attributes['height'])
    && isset($attributes['length'])
    && isset($attributes['width'])
) {
    $product = new Furniture($attributes);
}
$product->save();

Response::json(['message' => 'Success']);