<?php

use Timkrysta\Api;
use Timkrysta\Response;
use Timkrysta\Models\Product;

require_once $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' .'vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['GET']);

Response::json(
    isset($_GET['sku'])
        ? Product::findBySku($_GET['sku'])
        : Product::all()
);