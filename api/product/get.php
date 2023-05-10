<?php

use Timkrysta\Api;
use Timkrysta\Response;
use Timkrysta\Models\Product;

require_once __DIR__ . '/../../vendor/autoload.php';

Api::exitIfRequestMethodNotSupported(['GET']);

Response::json(
    isset($_GET['sku'])
        ? Product::findBySku($_GET['sku'])
        : Product::all()
);