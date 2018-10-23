<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\PriceTrait;

class TradeLimitMaker extends Trade
{
    use PriceTrait;

    /**
     * @param string       $action
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param string       $type
     * @param float        $volume
     * @param float        $price
     * @param string|null  $responseType
     */
    public function __construct(string $action, CurrencyPair $currencyPair, string $side, string $type, float $volume, float $price, ?string $responseType = null)
    {
        parent::__construct($action, $currencyPair, $side, $type, $volume, $responseType);

        $this->price = $price;
    }
}