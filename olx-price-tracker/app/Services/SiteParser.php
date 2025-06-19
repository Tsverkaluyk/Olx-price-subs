<?php

namespace App\Services;

abstract class SiteParser
{
    /**
     * Отримати ціну та валюту з URL
     *
     * @param  string  $url
     * @return array{price: float, currency: string}|null
     */
    abstract public function getPrice(string $url): ?array;
}
