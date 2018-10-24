<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\Part\StopPriceTrait;
use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;

class OrderStopLoss extends OrderOpen
{
    use StopPriceTrait;

    /** @var string */
    protected $type = OrderTypeInterface::STOP_LOSS;

    /**
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param float        $volume
     * @param float        $stopPrice
     * @param string|null  $clientOrderId
     * @param string|null  $responseType
     */
    public function __construct(CurrencyPair $currencyPair, string $side, float $volume, float $stopPrice, ?string $clientOrderId = null, ?string $responseType = null)
    {
        parent::__construct($currencyPair, $side, $volume, $clientOrderId, $responseType);

        $this->stopPrice = $stopPrice;
    }
}