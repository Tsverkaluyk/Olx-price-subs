<?php

namespace Tests\Feature;

use App\Services\OlxParser;
use Tests\TestCase;

class OlxParserLiveTest extends TestCase
{
    public function test_get_price_live()
    {
        $parser = new OlxParser();

        $url = 'https://www.olx.ua/d/uk/obyavlenie/peretvoryuvach-chastoti-11kvt-380v-IDUG7MQ.html?reason=hp%7Cpromoted';

        $result = $parser->getPrice($url);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('currency', $result);
        $this->assertIsFloat($result['price']);
        $this->assertIsString($result['currency']);
    }
}
