<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ApiTest extends TestCase
{
    protected Client $client;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://localhost/']);
    }
}
