<?php
require $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' .'vendor/autoload.php';

use Timkrysta\Api;
use Timkrysta\Validator;
use Timkrysta\Response;
use Timkrysta\Models\Product;

Api::exitIfHttpMethodNotIn(['POST']);


# TODO(tim): this is very ugly kind of validation
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

# TODO(tim): validation fails even if we pass X good ids to delete and one non existing
if ($validator->fails()) {
    Response::json([
        'message' => 'Validation Failed',
        'error' => $validator->errors,
    ], 422);
}


Product::delete($_POST['ids']);

Response::json([
    'message' => 'Success',
    'deleted_ids' => array_values(array_unique($_POST['ids'] ?? []))
], 200);

