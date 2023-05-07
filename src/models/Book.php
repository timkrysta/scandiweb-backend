<?php

namespace Timkrysta\Models;
use Timkrysta\Models\Product;

class Book extends Product {
  static protected $fillable = ['sku', 'name', 'price', 'weight'];

  public function __construct($attributes = []) {    
    $attributes = array_intersect_key($attributes, array_flip(self::$fillable));
    foreach ($attributes as $column => $value) {
      $this->$column = $value;
    }
  }
}
