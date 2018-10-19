<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\StopPriceTrait;

class TradeStopLoss extends Trade
{
    use StopPriceTrait;

    /**
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param string       $type
     * @param float        $volume
     * @param float        $stopPrice
     * @param string|null  $responseType
     */
    public function __construct(CurrencyPair $currencyPair, string $side, string $type, float $volume, float $stopPrice, ?string $responseType = null)
    {
        parent::__construct($currencyPair, $side, $type, $volume, $responseType);

        $this->stopPrice = $stopPrice;
    }
}