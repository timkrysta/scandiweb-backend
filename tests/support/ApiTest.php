<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ApiTest extends TestCase
{
    protected Client $client;
    protected const STORE_API_ENDPOINT       = '/api/product/saveApi.php';
    protected const GET_API_ENDPOINT         = '/api/product/get.php';
    protected const BULK_DELETE_API_ENDPOINT = '/api/product/bulkDelete.php';

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://localhost:8080']);
    }

    protected function getProduct($attributes = [])
    {
        $product = [
            "sku" => "ProductSku" . uniqid(),
            "name" => "ProductName",
            "price" => 100,
            "productType" => "dvd",
            "size" => null,
            "weight" => null,
            "height" => null,
            "length" => null,
            "width" => null
        ];
        $product = array_merge($product, $attributes);

        if ($product['productType'] === 'dvd') {
            $product['size'] = $product['size'] ?? rand(1, 1000);
        } elseif ($product['productType'] === 'book') {
            $product['weight'] = $product['weight'] ?? rand(1, 20);
        } elseif ($product['productType'] === 'furniture') {
            $product['height'] = $product['height'] ?? rand(1, 100);
            $product['length'] = $product['length'] ?? rand(1, 100);
            $product['width']   = $product['width'] ?? rand(1, 100);
        }

        return $product;
    }
    
    protected function addProduct($attributes = [])
    {
        $response = $this->client->post(self::STORE_API_ENDPOINT, [
            'form_params' => $this->getProduct($attributes)
        ]);
        return $response;
    }
}
