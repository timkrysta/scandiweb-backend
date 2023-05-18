<?php

namespace Timkrysta\Models;

use Timkrysta\Models\Product;

class Furniture extends Product
{
    /**
     * Furniture's specific properties
     */
    protected $height;
    protected $length;
    protected $width;

    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
        $this->height = $attributes['height'];
        $this->length = $attributes['length'];
        $this->width  = $attributes['width'];
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'height' => $this->height,
            'length' => $this->length,
            'width'  => $this->width
        ]);
    }
}
