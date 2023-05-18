<?php

use Timkrysta\Api;
use Timkrysta\Response;
use Timkrysta\ProductValidator;
use Timkrysta\Models\Product;

require_once __DIR__ . '/../../vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['POST']);

$validator = ProductValidator::validateStoreRequest();

if ($validator->fails()) {
    Response::validationFailed($validator->errors);
}

$attributes = $validator->validated();

Product::create($attributes);

Response::json(['message' => 'Success']);
