<?php

namespace Nidavellir\Apis;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Nidavellir\Abstracts\Classes\AbstractCrawler;
use Nidavellir\Cube\Models\Api;

class Coingecko
{
    public static function __callStatic($method, $args)
    {
        return CoingeckoService::new()->{$method}(...$args);
    }
}

class CoingeckoService extends AbstractCrawler
{
    public static function new(...$args)
    {
        return new self(...$args);
    }

    // ***** Api operations *****

    public function allTokens()
    {
        $client = new CoinGeckoClient();
        $this->execute(function () use ($client) {
            return $client->coins()->getList();
        });

        return $this;
    }
}
