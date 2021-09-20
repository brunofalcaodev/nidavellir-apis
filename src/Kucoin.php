<?php

namespace Nidavellir\Apis;

use KuCoin\SDK\Auth;
use KuCoin\SDK\KuCoinApi;
use KuCoin\SDK\PublicApi\Symbol;
use Nidavellir\Abstracts\Contracts\Pollable;
use Nidavellir\Cube\Models\Api;
use Nidavellir\Cube\Models\Exchange;

class Kucoin
{
    public static function __callStatic($method, $args)
    {
        return KucoinService::new()->{$method}(...$args);
    }
}

class KucoinService implements Pollable
{
    protected $api;
    protected $auth;
    protected $response;

    public function __construct()
    {
        if (env('KUCOIN_SANDBOX') == '1') {
            KuCoinApi::setBaseUri(Exchange::firstWhere('canonical', 'kucoin')->sandbox_api_url);
        }
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
        $this->auth = new Auth(
            $api->api_key,
            $api->api_secret,
            $api->api_passphrase,
            Auth::API_KEY_VERSION_V2
        );

        return $this;
    }

    public function response()
    {
        return $this->response;
    }

    // ***** Api operations *****
    public function allTickers()
    {
        $symbol = new Symbol($this->auth);
        $this->response = $symbol->getAllTickers();

        return $this;
    }
}
