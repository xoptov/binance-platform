<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\PriceTrait;
use Xoptov\BinancePlatform\Model\IcebergTrait;
use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\TimeInForceTrait;

class TradeLimit extends Trade
{
    use PriceTrait;

    use TimeInForceTrait;

    use IcebergTrait;

    /**
     * @param string       $action
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param string       $type
     * @param float        $volume
     * @param string       $timeInForce
     * @param float        $price
     * @param float|null   $icebergVolume
     * @param string|null  $responseType
     */
    public function __construct(string $action, CurrencyPair $currencyPair, string $side, string $type, float $volume, string $timeInForce, float $price, ?float $icebergVolume = null, ?string $responseType = null)
    {
        parent::__construct($action, $currencyPair, $side, $type, $volume, $responseType);

        $this->timeInForce = $timeInForce;
        $this->price = $price;
        $this->icebergVolume = $icebergVolume;
    }
}