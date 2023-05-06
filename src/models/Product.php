<?php

namespace Timkrysta\Models;

use Timkrysta\DB;
use Timkrysta\Validator;
use Timkrysta\Response;

abstract class Product {
    /* 
     * Get specific product
     */
    public static function findBySku($sku): array
    {
        $validator = new Validator(['sku' => $sku], validationRules: [
            'sku' => ['required', 'string', 'between:1,255', 'alpha_dash', 'exists:products,sku'],
        ]);

        if ($validator->fails()) {
            Response::json([
                'message' => 'Validation Failed',
                'error' => $validator->errors,
            ], 422);
        }

        $db = new DB();
        $result = $db->select(
            "SELECT * FROM products WHERE sku = ?;", 
            's', 
            [ $_GET['sku'] ]
        );
        if ($result === null) {
            Response::json(['message' => 'Getting the product failed'], 422);
        }
        return $result[0];
    }

    /* 
     * Get all products
     */
    public static function all(): array
    {
        $db = new DB();
        $result = $db->select("SELECT * FROM products;");
        if ($result === null) {
            Response::json(['message' => 'Getting products failed'], 422);
        }
        return $result;
    }
}