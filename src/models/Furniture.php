<?php

namespace Timkrysta\Models;
use Timkrysta\Models\Product;

class Furniture extends Product {
  static protected array $fillable = ['sku', 'name', 'price', 'height', 'length', 'width'];

  public function __construct($attributes = []) {    
    $attributes = array_intersect_key($attributes, array_flip(self::$fillable));
    foreach ($attributes as $column => $value) {
      $this->$column = $value;
    }
  }
}
