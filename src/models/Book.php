<?php

namespace Timkrysta\Models;

use Timkrysta\Models\Product;

class Book extends Product
{
    public $sku;
    public $name;
    public $price;
    public $weight;

    /**
     * The list of columns that can be saved to the database.
     *
     * @var string[]
     */
    static protected array $fillable = [
        'sku',
        'name',
        'price',
        'weight'
    ];

    public function __construct(array $attributes = [])
    {
        $attributes = array_intersect_key($attributes, array_flip(self::$fillable));
        foreach ($attributes as $column => $value) {
            $this->$column = $value;
        }
    }
}
