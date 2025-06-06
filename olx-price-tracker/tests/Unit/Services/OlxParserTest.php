<?php

namespace Tests\Unit\Services;

use App\Services\OlxParser;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;

class OlxParserTest extends TestCase
{
    protected OlxParser $parser;
    protected Client $mockClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = $this->createMock(\GuzzleHttp\Client::class);
        $this->parser = new \App\Services\OlxParser();

        $reflection = new \ReflectionClass($this->parser);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->parser, $this->mockClient);

        \Illuminate\Support\Facades\Cache::shouldReceive('remember')
            ->andReturnUsing(function ($key, $time, $callback) {
                return $callback();
            });
    }

    public function test_get_price_success()
    {
        $html = <<<HTML
<div data-testid="ad-price-container">
  <h3>1 234 567 грн</h3>
</div>
HTML;

        $response = new Response(200, [], $html);

        $this->mockClient->method('get')->willReturn($response);

        $result = $this->parser->getPrice('www.olx.ua/');

        $this->assertIsArray($result);
        $this->assertEquals(1234567, $result['price']);
        $this->assertEquals('грн', $result['currency']);
    }

    public function test_get_price_decimal_success()
    {
        $html = <<<HTML
<div data-testid="ad-price-container">
  <h3>0.33 грн</h3>
</div>
HTML;

        $response = new Response(200, [], $html);

        $this->mockClient->method('get')->willReturn($response);

        $result = $this->parser->getPrice('www.olx.ua/');

        $this->assertIsArray($result);
        $this->assertEquals(0.33, $result['price']);
        $this->assertEquals('грн', $result['currency']);
    }

    public function test_get_price_non_200_response_returns_null()
    {
        $response = new Response(404);

        $this->mockClient->method('get')->willReturn($response);

        $result = $this->parser->getPrice('www.olx.ua/');

        $this->assertNull($result);
    }

    public function test_get_price_html_without_price_returns_null()
    {
        $html = <<<HTML
<div>
    <h3>No price here</h3>
</div>
HTML;

        $response = new Response(200, [], $html);

        $this->mockClient->method('get')->willReturn($response);

        $result = $this->parser->getPrice('www.olx.ua/');

        $this->assertNull($result);
    }

    public function test_get_price_http_exception_returns_null()
    {
        $this->mockClient->method('get')->willThrowException(new \Exception('Network error'));

        $result = $this->parser->getPrice('www.olx.ua/');

        $this->assertNull($result);
    }
}

