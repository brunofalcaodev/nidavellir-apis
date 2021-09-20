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

    public function __construct()
    {
        //
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }

    public function withApi(Api $api)
    {
        $this->api = $api;

        return $this;
    }

    public function connect()
    {
        return $this;
    }

    public function response()
    {
        return $this->response;
    }

    public function canExecute()
    {
        return true;
    }

    public function execute(callable $function)
    {
        try {
            $this->response = $function();
        } catch (\Exception $e) {
            // Exception saved in the crawlers error log table.
            throw new $e();
        }
    }

    // ***** Api operations *****
    public function allTickers()
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
