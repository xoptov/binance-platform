<?php

namespace Xoptov\BinancePlatform;

use Binance\API;
use Binance\RateLimiter;
use Xoptov\BinancePlatform\Model\Currency;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class Exchange
{
    /** @var bool */
    private static $created = false;

    /** @var API */
    private $api;

    /** @var Currency[] */
    private $currencies = array();

    /** @var CurrencyPair[] */
    private $currencyPairs = array();

    /**
     * @param RateLimiter $api
     * @param bool|null   $init
     * @return null|Exchange
     * @throws \Exception
     */
    public static function create(RateLimiter $api, ?bool $init = true): ?self
    {
        if (self::$created) {
            return null;
        }

        return new self($api, $init);
    }

    /**
     * @param RateLimiter $api
     * @param bool        $init
     * @throws \Exception
     */
    private function __construct(RateLimiter $api, bool $init)
    {
        self::$created = true;

        $this->api = $api;

        if ($init) {
            $this->loadInformation();
        }
    }

    /**
     * @param string $symbol
     * @return bool
     */
    public function hasCurrency(string $symbol): bool
    {
        return isset($this->currencies[$symbol]);
    }

    /**
     * @param string $symbol
     * @return null|Currency
     */
    public function getCurrency(string $symbol): ?Currency
    {
        if ($this->hasCurrency($symbol)) {
            return $this->currencies[$symbol];
        }

        return null;
    }

    /**
     * @param string $symbol
     * @return bool
     */
    public function hasCurrencyPair(string $symbol): bool
    {
        return isset($this->currencyPairs[$symbol]);
    }

    /**
     * @param string $symbol
     * @return null|CurrencyPair
     */
    public function getCurrencyPair(string $symbol): ?CurrencyPair
    {
        if ($this->hasCurrencyPair($symbol)) {
            return $this->currencyPairs[$symbol];
        }

        return null;
    }

    /**
     * @param Currency $currency
     * @return bool
     */
    private function addCurrency(Currency $currency): bool
    {
        if ($this->hasCurrency($currency)) {
            return false;
        }

        $this->currencies[$currency->getSymbol()] = $currency;

        return true;
    }

    /**
     * @param CurrencyPair $currencyPair
     * @return bool
     */
    private function addCurrencyPair(CurrencyPair $currencyPair): bool
    {
        if ($this->hasCurrencyPair($currencyPair)) {
            return false;
        }

        $this->currencyPairs[$currencyPair->getSymbol()] = $currencyPair;

        return true;
    }

    /**
     * @throws \Exception
     */
    private function loadInformation(): void
    {
        $result = $this->api->exchangeInfo();

        foreach ($result["symbols"] as $item) {

            if ($this->hasCurrencyPair($item["symbol"])) {
                continue;
            }

            $base = $this->getCurrency($item["baseAsset"]);

            if (!$base) {
                $base = new Currency($item["baseAsset"]);
                $this->addCurrency($base);
            }

            $quote = $this->getCurrency($item["quoteAsset"]);

            if (!$quote) {
                $quote = new Currency($item["quoteAsset"]);
                $this->addCurrency($quote);
            }

            $currencyPair = new CurrencyPair($base, $quote, $item["status"], $item["orderTypes"], $item["icebergAllowed"], $item["filters"]);
            $this->addCurrencyPair($currencyPair);
        }
    }
}