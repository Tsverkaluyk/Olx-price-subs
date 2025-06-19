<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;
use Exception;

class OlxParser extends SiteParser
{
    public const PRICE_SELECTOR = '[data-testid="ad-price-container"] h3';
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param  string  $url
     * @return array|null
     */
    public function getPrice(string $url): ?array
    {
        $cacheKey = 'olx_price_'.md5($url);
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($url) {
            try {
                $response = $this->client->get($url);

                if ($response->getStatusCode() !== 200) {
                    return null;
                }

                $html = (string) $response->getBody();

                $crawler = new Crawler($html);

                $priceElement = $crawler->filter(self::PRICE_SELECTOR);
                $priceText = trim($priceElement->text());

                preg_match('/([\d\s,.]+)\s*([^\d]+)/', $priceText, $matches);

                if (count($matches) < 3) {
                    return null;
                }

                $price = (float) preg_replace('/\s+/', '', $matches[1]);
                $currency = trim($matches[2]);

                return [
                    'price' => $price,
                    'currency' => $currency
                ];

            } catch (Exception $e) {
                return null;
            }
        });
    }

}
