<?php

declare(strict_types=1);

require_once __DIR__.'/../support/ApiTest.php';

use GuzzleHttp\Exception\ClientException;

final class BulkDeleteApiTest extends ApiTest
{
    public function test_request_without_ids_does_nothing(): void
    {
        try {
            $response = $this->client->post(self::BULK_DELETE_API_ENDPOINT, [
                'form_params' => []
            ]);
        } catch (ClientException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->assertSame(422, $response->getStatusCode());
    }
    
    public function test_deleting_a_single_product_works(): void
    {
        try {
            $this->addProduct();

            $response = $this->client->get(self::GET_API_ENDPOINT);
            $body = $response->getBody()->getContents();
            $firstJsonResponse = json_decode($body, true);


            $response = $this->client->post(self::BULK_DELETE_API_ENDPOINT, [
                'form_params' => [
                    'ids' => [
                        $firstJsonResponse[0]['id']
                    ]
                ]
            ]);
            $body = $response->getBody()->getContents();
            $jsonResponse = json_decode($body, true);
            $this->assertEquals('Success', $jsonResponse['message']);
            $this->assertSame(200, $response->getStatusCode());


            $response = $this->client->get(self::GET_API_ENDPOINT);
            $body = $response->getBody()->getContents();
            $secondJsonResponse = json_decode($body, true);
            if ($firstJsonResponse[0]['id'] == $secondJsonResponse[0]['id']) {
                $this->assertTrue(false);
            }
        } catch (ClientException $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertTrue(true);
    }
    
    public function test_deleting_multiple_products_works(): void
    {
        try {
            $this->addProduct();
            $this->addProduct();

            $response = $this->client->get(self::GET_API_ENDPOINT);
            $body = $response->getBody()->getContents();
            $firstJsonResponse = json_decode($body, true);


            $response = $this->client->post(self::BULK_DELETE_API_ENDPOINT, [
                'form_params' => [
                    'ids' => [
                        $firstJsonResponse[0]['id'],
                        $firstJsonResponse[1]['id']
                    ]
                ]
            ]);
            $body = $response->getBody()->getContents();
            $jsonResponse = json_decode($body, true);
            $this->assertEquals('Success', $jsonResponse['message']);
            $this->assertSame(200, $response->getStatusCode());


            $response = $this->client->get(self::GET_API_ENDPOINT);
            $body = $response->getBody()->getContents();
            $secondJsonResponse = json_decode($body, true);
            if (
                $firstJsonResponse[0]['id'] == $secondJsonResponse[0]['id']
                && $firstJsonResponse[1]['id'] == $secondJsonResponse[1]['id']
            ) {
                $this->assertTrue(false);
            }
        } catch (ClientException $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertTrue(true);
    }
}
