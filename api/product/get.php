<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' .'vendor/autoload.php';

use Timkrysta\Api;
use Timkrysta\Models\Product;
use Timkrysta\Response;

Api::exitIfHttpMethodNotIn(['GET']);

$data = isset($_GET['sku'])
    ? Product::findBySku($_GET['sku'])
    : Product::all();

Response::json($data, 200);
