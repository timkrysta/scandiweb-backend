<?php

namespace Timkrysta\Models;

use Timkrysta\DB;
use Timkrysta\Validator;
use Timkrysta\Response;

abstract class Product {
    static protected $allowedColumns = [];

    /*
     * Get attributes
     */
    public function attributes()
    {
        $attributes = [];
        foreach (static::$allowedColumns as $column) {
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    /* 
     * Save the product
     */
    public function save() {
        // We don't need to sanitize the input values yourself because the prepared statement will automatically sanitize the values for you. 
        $attributes = $this->attributes();

        $db = new DB();
        $columns = implode(', ', array_keys($attributes));
        $values  = DB::getQuestionMarksString(count($attributes));
        $insertId = $db->insert(
            "INSERT INTO products ({$columns}) VALUES ({$values});",
            'ssdiiiii',
            array_values($attributes)
        );

        if ($insertId === null) {
            Response::json(['message' => 'Insertion failed'], 422);
        }
    }

    /* 
     * Get specific product
     */
    public static function findBySku($sku): array
    {
        $validator = new Validator([
            'sku' => $sku
        ], [
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

    /* 
     * Delete product(s)
     */
    public static function delete(array $ids): void
    {
        $db = new DB();
        $values = DB::getQuestionMarksString(count($ids));
        $db->execute(
            "DELETE FROM products WHERE id IN ({$values})", 
            str_repeat('i', count($ids)), 
            $ids
        );
    }
}