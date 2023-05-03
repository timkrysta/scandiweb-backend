<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' .'vendor/autoload.php';

use Timkrysta\Api;
use Timkrysta\DB;
use Timkrysta\Validator;
use Timkrysta\Response;

Api::exitIfHttpMethodNotIn(['GET']);



$db = new DB();
$data = [];

if (isset($_GET['sku'])) {
    // return specific product
    $validationRules = [
        'sku' => ['required', 'string', 'between:1,255', 'alpha_dash', 'exists:products,sku'],
    ];
    $validator = new Validator($_GET, $validationRules);

    if ($validator->fails()) {
        Response::json([
            'message' => 'Validation Failed',
            'error' => $validator->errors,
        ], 400);
    }

    $query = "SELECT * FROM products WHERE sku = ?;";
    $paramType = 's';
    $paramArray = [
        $_GET['sku']
    ];
    $result = $db->select($query, $paramType, $paramArray);
    if ($result === null) {
        Response::json(['message' => 'Getting the product failed'], 400);
    }
    $data = $result;
} 
else {
    $query = "SELECT * FROM products;";
    $result = $db->select($query);
    if ($result === null) {
        Response::json(['message' => 'Getting products failed'], 400);
    }
    $data = $result;
}


Response::json($data, 200);
