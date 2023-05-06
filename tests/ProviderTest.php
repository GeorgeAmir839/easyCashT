<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Tests\TestCase;
use Laravel\Lumen\Testing\TestCase;

class ProviderTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    public function testProvider()
    {

        $response = $this->json('GET', '/api/v1/transactaions');
        $response->assertResponseStatus(200);
        $response->seeJson(['message' => 'Hello, world!']);
    }
    //    ./vendor/bin/phpunit --filter ProviderTest::testProvider

    public function testExample()
    {
        $this->assertTrue(true);
    }
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}
