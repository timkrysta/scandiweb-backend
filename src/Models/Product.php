<?php

namespace Timkrysta\Models;

use Timkrysta\DB;
use Timkrysta\ProductValidator;
use Timkrysta\Response;

abstract class Product
{
    /**
     * Universal properties of every product
     */
    protected $sku;
    protected $name;
    protected $price;

    public function __construct(array $attributes)
    {
        $this->sku   = $attributes['sku'];
        $this->name  = $attributes['name'];
        $this->price = $attributes['price'];
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'sku'   => $this->sku,
            'name'  => $this->name,
            'price' => $this->price,
        ];
    }
    
    /**
     * Create a new product from an array of attributes without using conditionals to handle product differences
     *
     * @param  array $attributes
     * @return void
     */
    public static function create(array $attributes): void
    {
        $className = ucfirst(strtolower($attributes['productType']));
        $fullyQualifiedClassName = self::getNamespace() . '\\' . $className;

        if (
            !class_exists($fullyQualifiedClassName)
            || !is_subclass_of($fullyQualifiedClassName, Product::class)
        ) {
            Response::json(['message' => 'Invalid product type'], 422);
        }

        $product = new $fullyQualifiedClassName($attributes);
        $product->save();
    }

    /**
     * Save the product to the database.
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
            str_repeat('s', count($attributes)),
            array_values($attributes)
        );

        if ($insertId === null) {
            Response::json(['message' => 'Insertion failed'], 422);
        }
        return $insertId;
    }

    /**
     * Get a product by SKU.
     *
     * @param string $sku The SKU to search for
     * @return array The product with the specified SKU
     */
    public static function findBySku(string $sku): array
    {
        $validator = ProductValidator::validateFindBySkuRequest($sku);

        if ($validator->fails()) {
            Response::validationFailed($validator->errors);
        }

        $db = new DB();
        $result = $db->select(
            "SELECT * FROM products WHERE sku = ?;",
            's',
            [$sku]
        );

        if ($result === null) {
            Response::json(['message' => 'Getting the product failed'], 422);
        }
        return $result[0];
    }

    /** 
     * Get all products.
     *
     * @return array All products
     */
    public static function all(): array
    {
        $db = new DB();
        $result = $db->select("SELECT * FROM products;");
        if ($result === null) {
            Response::json([]);
        }
        return $result;
    }

    /** 
     * Delete products by IDs.
     *
     * @param int[] $productIds The IDs of the products to delete
     */
    public static function delete(array $productIds): void
    {
        $productIds = array_values(array_unique($productIds ?? []));

        $db = new DB();
        $placeholders = DB::getPlaceholders(count($productIds));
        $db->execute(
            "DELETE FROM products WHERE id IN ({$placeholders})",
            str_repeat('i', count($productIds)),
            $productIds
        );
    }

    public static function getNamespace(): string
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $namespace = $reflectionClass->getNamespaceName();
        return $namespace;
    }
}
