<?php

namespace Timkrysta\Models;

use Timkrysta\Models\Product;

class Dvd extends Product
{
    /**
     * Dvd's specific properties
     */
    protected $size;

    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
        $this->size = $attributes['size'];
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'size' => $this->size
        ]);
    }
}
