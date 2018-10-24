<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\Part\PriceTrait;
use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;

class OrderLimitMaker extends OrderOpen
{
    use PriceTrait;

    /** @var string */
    protected $type = OrderTypeInterface::LIMIT_MAKER;

    /**
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param float        $volume
     * @param float        $price
     * @param string|null  $clientOrderId
     * @param string|null  $responseType
     */
    public function __construct(CurrencyPair $currencyPair, string $side, float $volume, float $price, ?string $clientOrderId = null, ?string $responseType = null)
    {
        parent::__construct($currencyPair, $side, $volume, $clientOrderId, $responseType);

        $this->price = $price;
    }
}