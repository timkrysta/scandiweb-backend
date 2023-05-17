<?php

declare(strict_types=1);

require_once __DIR__.'/../support/ApiTest.php';

use GuzzleHttp\Exception\ClientException;

final class GetApiTest extends ApiTest
{
    public function test_getting_single_product_success(): void
    {
        try {
            $product = $this->getProduct();
            $this->addProduct($product);

            $response = $this->client->get(self::GET_API_ENDPOINT, [
                'query' => [
                    "sku" => $product['sku'],
                ]
            ]);

            $body = $response->getBody()->getContents();
            $jsonResponse = json_decode($body, true);
            $this->assertEquals($product['sku'], $jsonResponse['sku']);
        } catch (ClientException $e) {
            $this->assertTrue(false);
            return;
        }
    }

    public function test_getting_all_products_success(): void
    {
        try {
            $product1 = $this->getProduct();
            $this->addProduct($product1);
            
            $product2 = $this->getProduct();
            $this->addProduct($product2);

            $response = $this->client->get(self::GET_API_ENDPOINT);

            $body = $response->getBody()->getContents();
            
            $jsonResponse = json_decode($body, true);

            foreach ($jsonResponse as $product) {
                if (in_array($product1['sku'], $product)) {
                    $this->assertTrue(true);
                }
                if (in_array($product2['sku'], $product)) {
                    $this->assertTrue(true);
                }
            }
        } catch (ClientException $e) {
            $this->assertTrue(false);
            return;
        }
    }

    
}
