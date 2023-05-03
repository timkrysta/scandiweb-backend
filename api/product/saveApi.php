<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' .'vendor/autoload.php';

use Timkrysta\Api;
use Timkrysta\DB;
use Timkrysta\Validator;
use Timkrysta\Response;

Api::exitIfHttpMethodNotIn(['POST']);

$validationRules = [
    'sku'         => ['required', 'string', 'between:1,255', 'alpha_dash', 'unique:products,sku'],
    'name'        => ['required', 'string', 'between:1,255', 'alpha_dash'],
    'price'       => ['required', 'numeric', 'between:0.01,9999999999.99'],
    'productType' => ['required', 'in:dvd,book,furniture'],
    'size'        => ['numeric', 'between:1,32767', 'required_if:productType,dvd'],
    'weight'      => ['numeric', 'between:1,32767', 'required_if:productType,book'],
    'height'      => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
    'length'      => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
    'width'       => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
];


$validator = new Validator($_POST, $validationRules);


if ($validator->fails()) {
    Response::json([
        'message' => 'Validation Failed',
        'error' => $validator->errors,
    ], 400);
}




$attributes = [
    'sku'    => $_POST['sku']    ?? null,
    'name'   => $_POST['name']   ?? null,
    'price'  => $_POST['price']  ?? null,
    'size'   => $_POST['size']   ?? null,
    'weight' => $_POST['weight'] ?? null,
    'height' => $_POST['height'] ?? null,
    'length' => $_POST['length'] ?? null,
    'width'  => $_POST['width']  ?? null,
];
$db = new DB();
$columns = implode(', ', array_keys($attributes));
$values  = implode(', ', array_fill(0, count($attributes), '?')); // ?, ?, ? ...
$insertId = $db->insert(
    "INSERT INTO products ({$columns}) VALUES ({$values});",
    'ssdiiiii',
    array_values($attributes)
);

if ($insertId === null) {
    Response::json(['message' => 'Insertion failed'], 400);
}



Response::json([
    'message' => 'Success',
    'obj' => $attributes
], 200);
