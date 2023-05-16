<?php

use Timkrysta\Api;
use Timkrysta\Validator;
use Timkrysta\Response;
use Timkrysta\Models\Book;
use Timkrysta\Models\DVD;
use Timkrysta\Models\Furniture;

require_once __DIR__ . '/../../vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['POST']);

$validator = new Validator($_POST, [
    'sku'         => ['required', 'string', 'between:1,255', 'alpha_dash', 'unique:products,sku'],
    'name'        => ['required', 'string', 'between:1,255'],
    'price'       => ['required', 'numeric', 'between:0.01,9999999999.99'],
    'productType' => ['required', 'in:dvd,book,furniture'],
    'size'        => ['numeric', 'between:1,32767', 'required_if:productType,dvd'],
    'weight'      => ['numeric', 'between:1,32767', 'required_if:productType,book'],
    'height'      => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
    'length'      => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
    'width'       => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
]);

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
