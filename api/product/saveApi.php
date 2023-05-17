<?php

use Timkrysta\Api;
use Timkrysta\Response;
use Timkrysta\ProductValidator;

require_once __DIR__ . '/../../vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['POST']);

$validator = ProductValidator::validateStoreRequest();

if ($validator->fails()) {
    Response::validationFailed($validator->errors);
}

$attributes = $validator->validated();

$className = ucfirst(strtolower($attributes['productType']));
$fullyQualifiedClassName = "Timkrysta\\Models\\{$className}";
if (
    !class_exists($fullyQualifiedClassName)
    || !is_subclass_of($fullyQualifiedClassName, \Timkrysta\Models\Product::class)
) {
    Response::json([
        'message' => "Invalid product type: {$className}"
    ], 422);
}
$product = new $fullyQualifiedClassName($attributes);
$product->save();

Response::json(['message' => 'Success']);