<?php

namespace Nidavellir\Apis;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Nidavellir\Abstracts\Contracts\Pollable;
use Nidavellir\Cube\Models\Api;

class Coingecko
{
    public static function __callStatic($method, $args)
    {
        return CoingeckoService::new()->{$method}(...$args);
    }
}

class CoingeckoService implements Pollable
{
    protected $api;
    protected $auth;
    protected $response;

    // ***** Api operations *****
    public function allTokens()
    {
        $client = new CoinGeckoClient();
        $this->execute(function () use ($client) {
            return $client->coins()->getList();
        });

        return $this;
    }

    public function allMarkets(array $params = [], string $currency = 'usd')
    {
        $client = new CoinGeckoClient();
        $this->execute(function () use ($client, $currency, $params) {
            return $client->coins()->getMarkets($currency, $params);
        });

        return $this;
    }
}
