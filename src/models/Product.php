<?php

namespace Timkrysta\Models;

use Timkrysta\DB;
use Timkrysta\Validator;
use Timkrysta\Response;

abstract class Product {
    /**
     * The list of columns that can be saved to the database
     *
     * @var string[]
     */
    protected static array $fillable = [];

    /**
     * Get attributes
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [];
        foreach (static::$fillable as $column) {
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    /**
     * Save the product
     *
     * @return int
     */
    public function save(): int
    {
        $attributes = $this->attributes();

        $db = new DB();
        $columns = implode(', ', array_keys($attributes));
        $placeholders  = DB::getPlaceholders(count($attributes));
        $insertId = $db->insert(
            "INSERT INTO products ({$columns}) VALUES ({$placeholders});",
            'ssdiiiii',
            array_values($attributes)
        );

        if ($insertId === null) {
            Response::json(['message' => 'Insertion failed'], 422);
        }
        return $insertId;
    }

    /**
     * Get a product by SKU
     *
     * @param string $sku The SKU to search for
     * @return array The product with the specified SKU
     */
    public static function findBySku(string $sku): array
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
            [ $sku ]
        );

        if ($result === null) {
            Response::json(['message' => 'Getting the product failed'], 422);
        }
        return $result[0];
    }

    /** 
     * Get all products
     *
     * @return array All products
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

    /** 
     * Delete products by IDs
     *
     * @param int[] $productIds The IDs of the products to delete
     */
    public static function delete(array $productIds): void
    {
        $db = new DB();
        $placeholders = DB::getPlaceholders(count($productIds));
        $db->execute(
            "DELETE FROM products WHERE id IN ({$placeholders})", 
            str_repeat('i', count($productIds)), 
            $productIds
        );
    }
}