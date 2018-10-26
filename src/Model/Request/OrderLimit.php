<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\Part\PriceTrait;
use Xoptov\BinancePlatform\Model\Part\IcebergTrait;
use Xoptov\BinancePlatform\Model\Part\TimeInForceTrait;
use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;

class OrderLimit extends OrderOpen
{
    use PriceTrait;

    use TimeInForceTrait;

    use IcebergTrait;

    /**
     * @var string
     */
    protected $type = OrderTypeInterface::LIMIT;

    /**
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param float        $volume
     * @param string       $timeInForce
     * @param float        $price
     * @param float|null   $icebergVolume
     * @param string|null  $clientOrderId
     * @param string|null  $responseType
     */
    public function __construct(CurrencyPair $currencyPair, string $side, float $volume, string $timeInForce,
                                float $price, ?float $icebergVolume = null, ?string $clientOrderId = null,
                                ?string $responseType = null)
    {
        parent::__construct($currencyPair, $side, $volume, $clientOrderId, $responseType);

        $this->timeInForce = $timeInForce;
        $this->price = $price;
        $this->icebergVolume = $icebergVolume;
    }
}