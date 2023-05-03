<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

/* TODO(tim): tests to be added yet

# add automatic test sku for unique
# add automatic test additional attributes if productType is changed
$testsThatShouldFail = [
    'sku'  => [null, '', ' ', '?', 0, 1, 'atnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhaneouantoehusnatoehustnaoheusaoe'],
    'name' => [null, '', ' ', '?', 0, 1, 'atnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhatnoehuntaoehunhaneouantoehusnatoehustnaoheusaoe'],
    'price' => [null, 'hi', 0, 19999999999.99],
    'size'   => [' ', 0, 32768],
    'weight' => [' ', 0, 32768],
    'heigth' => [' ', 0, 32768],
    'length' => [' ', 0, 32768],
    'width'  => [' ', 0, 32768],
];

$testThatShouldPass = [ // but need to verify the results
    'price'  => [0.0101200000121],
    'size'   => [0.01],
    'weight' => [0.01],
    'heigth' => [0.01],
    'length' => [0.01],
    'width'  => [0.01],
]; */

/* 
echo Psr7\Message::toString($e->getRequest());
echo Psr7\Message::toString($e->getResponse());
*/

final class ApiTest extends TestCase
{
    private Client $client;
    private const STORE_API_ENDPOINT = '/web-developer-test-assignment/api/product/saveApi.php';

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://localhost/']);
    }

    public function test_add_product_success(): void
    {
        $json = '{
            "sku":"ProductSku'.rand(0, 10000000).'",
            "name":"ProductName",
            "price":100,
            "productType":"dvd",
            "size":700,
            "weight":null,
            "heigth":null,
            "length":null,
            "width":null
        }';
        try {
            $response = $this->client->post(self::STORE_API_ENDPOINT, [
                'form_params' => json_decode($json, true)
            ]);
        } catch (ClientException $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertSame(200, $response->getStatusCode());
    }
    
    public function test_empty_add_request_fails(): void
    {
        try {
            $response = $this->client->post(self::STORE_API_ENDPOINT, [
                'form_params' => []
            ]);
        } catch (ClientException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->assertSame(400, $response->getStatusCode());
    }
}
