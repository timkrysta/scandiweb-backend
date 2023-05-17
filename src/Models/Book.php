<?php

namespace Timkrysta\Models;

use Timkrysta\Models\Product;

class Book extends Product
{
    /**
     * Book's specific properties
     */
    protected $weight;

    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
        $this->weight = $attributes['weight'];
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'weight' => $this->weight
        ]);
    }
}
