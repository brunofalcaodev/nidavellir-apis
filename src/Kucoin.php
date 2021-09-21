<?php

namespace Nidavellir\Apis;

use KuCoin\SDK\Auth;
use KuCoin\SDK\KuCoinApi;
use KuCoin\SDK\PublicApi\Symbol;
use Nidavellir\Abstracts\Contracts\Pollable;
use Nidavellir\Cube\Models\Api;
use Nidavellir\Cube\Models\Exchange;
use Nidavellir\Exceptions\ApiException;

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
    protected $system = false;
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

    public function response()
    {
        return $this->response;
    }

    public function execute(callable $function)
    {
        try {
            $this->response = $function();
        } catch (ApiException $e) {
            // Exception saved in the crawlers error log table.
            throw new $e();
        }
    }

    public function asSystem()
    {
        $this->system = true;

        return $this;
    }

    public function connect()
    {
        // System call.
        if ($this->system) {
            $this->auth = new Auth(
                env('KUCOIN_API_KEY'),
                env('KUCOIN_API_SECRET'),
                env('KUCOIN_API_PASSPHRASE'),
                Auth::API_KEY_VERSION_V2
            );

            // Create a system api instance, empty, dummy for logging purposes.
            $this->api = new Api();

            return $this;
        }

        // User call.
        $this->auth = new Auth(
            $this->api->api_key,
            $this->api->api_secret,
            $this->api->api_passphrase,
            Auth::API_KEY_VERSION_V2
        );

        return $this;
    }

    // ***** Api operations *****
    public function allTokens()
    {
        $this->execute(function () {
            $this->connect();
            $symbol = new Symbol($this->auth);

            return $symbol->getAllTickers();
        });

        return $this;
    }
}
