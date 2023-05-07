<?php
require $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' . 'vendor/autoload.php';

use Timkrysta\Api;
use Timkrysta\DB;
use Timkrysta\Validator;
use Timkrysta\Response;
use Timkrysta\Models\Book;
use Timkrysta\Models\DVD;
use Timkrysta\Models\Furniture;

Api::exitIfHttpMethodNotIn(['POST']);

$validationRules = [
    'sku'         => ['required', 'string', 'between:1,255', 'alpha_dash', 'unique:products,sku'],
    'name'        => ['required', 'string', 'between:1,255'],
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
    Response::validationFailed($validator->errors);
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

if ($attributes['weight'] !== null) {
    $book = new Book($attributes);
    $book->save();
}

if ($attributes['size'] !== null) {
    $dvd = new DVD($attributes);
    $dvd->save();
}

if ($attributes['height'] !== null &&
    $attributes['length'] !== null &&
    $attributes['width']  !== null
) {
    $furniture = new Furniture($attributes);
    $furniture->save();
}



Response::json([
    'message' => 'Success',
    'obj' => $attributes
]);
