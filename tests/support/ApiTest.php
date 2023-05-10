<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ApiTest extends TestCase
{
    protected Client $client;
    protected const STORE_API_ENDPOINT = '/web-developer-test-assignment/api/product/saveApi.php';
    protected const GET_API_ENDPOINT = '/web-developer-test-assignment/api/product/get.php';
    protected const BULK_DELETE_API_ENDPOINT = '/web-developer-test-assignment/api/product/bulkDelete.php';

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://localhost/']);
    }

    protected function addProduct()
    {
        $product = [
            "sku" => "ProductSku" . rand(0, 10000000),
            "name" => "ProductName",
            "price" => 100,
            "productType" => "dvd",
            "size" => 700,
            "weight" => null,
            "heigth" => null,
            "length" => null,
            "width" => null
        ];
        $this->client->post(self::STORE_API_ENDPOINT, [
            'form_params' => $product
        ]);
        return $product;
    }
}
