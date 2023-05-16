<?php

use Timkrysta\Api;
use Timkrysta\ProductValidator;
use Timkrysta\Response;
use Timkrysta\Models\Product;

require_once __DIR__ . '/../../vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['POST']);

$validator = ProductValidator::validateBulkDeleteRequest();

if ($validator->fails()) {
    Response::validationFailed($validator->errors);
}

Product::delete($_POST['ids']);

Response::json(['message' => 'Success']);