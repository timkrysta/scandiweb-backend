<?php

namespace Timkrysta\Models;

use Timkrysta\Models\Product;

class Furniture extends Product
{
    public $sku;
    public $name;
    public $price;
    public $height;
    public $length;
    public $width;

    /**
     * The list of columns that can be saved to the database.
     *
     * @var string[]
     */
    static protected array $fillable = [
        'sku', 
        'name', 
        'price', 
        'height', 
        'length', 
        'width'
    ];

    public function __construct(array $attributes = [])
    {
        $attributes = array_intersect_key($attributes, array_flip(self::$fillable));
        foreach ($attributes as $column => $value) {
            $this->$column = $value;
        }
    }
}
