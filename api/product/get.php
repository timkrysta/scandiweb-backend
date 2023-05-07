<?php
require $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' .'vendor/autoload.php';

use Timkrysta\Api;
use Timkrysta\Models\Product;
use Timkrysta\Response;

Api::exitIfRequestMethodNotSupported(['GET']);

$data = isset($_GET['sku'])
    ? Product::findBySku($_GET['sku'])
    : Product::all();

Response::json($data);
