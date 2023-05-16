<?php

namespace Timkrysta;

use Timkrysta\Validator;

class ProductValidator
{
    public static function validateStoreRequest(): Validator
    {
        return new Validator($_POST, [
            'sku'         => ['required', 'string', 'between:1,255', 'alpha_dash', 'unique:products,sku'],
            'name'        => ['required', 'string', 'between:1,255'],
            'price'       => ['required', 'numeric', 'between:0.01,9999999999.99'],
            'productType' => ['required', 'in:dvd,book,furniture'],
            'size'        => ['numeric', 'between:1,32767', 'required_if:productType,dvd'],
            'weight'      => ['numeric', 'between:1,32767', 'required_if:productType,book'],
            'height'      => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
            'length'      => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
            'width'       => ['numeric', 'between:1,32767', 'required_if:productType,furniture'],
        ]);
    }

    public static function validateFindBySkuRequest(string $sku): Validator
    {
        return new Validator([
            'sku' => $sku
        ], [
            'sku' => ['required', 'string', 'between:1,255', 'alpha_dash', 'exists:products,sku'],
        ]);
    }
    
    /**
     * This method validates:
     * 
     * 1. $_POST['ids'] to exist and be an array
     * 2. Every item in $_POST['ids'] array to be a number and existing id on products table
     *
     * @return Validator
     */
    public static function validateBulkDeleteRequest(): Validator
    {
        $validationData = [];
        $validationRules = [
            'ids' => ['required', 'array'],
        ];

        if (isset($_POST['ids']) && is_array($_POST['ids'])) {
            foreach ($_POST['ids'] as $index => $id) {
                $key = "ids.{$index}";
                $validationData[$key] = $id;
                $validationRules[$key] = ['numeric', 'exists:products,id'];
            }
            $validationData['ids'] = $_POST['ids'];
        }

        return new Validator($validationData, $validationRules);
    }
}
