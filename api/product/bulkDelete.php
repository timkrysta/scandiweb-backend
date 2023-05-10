<?php

use Timkrysta\Api;
use Timkrysta\Validator;
use Timkrysta\Response;
use Timkrysta\Models\Product;

require_once __DIR__ . '/../../vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['POST']);

$validationData = [];
$validationRules = [
    'ids' => ['required', 'array'],
];

if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    foreach ($_POST['ids'] as $index => $id) {
        $key = "ids.{$index}";
        $validationData[$key] = $id;
        $validationRules[$key] = ['numeric', 'exists:products,id'];
    }
    $validationData['ids'] = $_POST['ids'];
}

$validator = new Validator($validationData, $validationRules);

if ($validator->fails()) {
    Response::validationFailed($validator->errors);
}

Product::delete($_POST['ids']);

Response::json(['message' => 'Success']);