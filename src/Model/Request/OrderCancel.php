<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\Part\OrderIdTrait;

class OrderCancel extends Order
{
    use OrderIdTrait;

    /**
     * OrderCancel constructor.
     *
     * @param CurrencyPair $currencyPair
     * @param null|string  $orderId
     * @param null|string  $clientOrderId
     */
    public function __construct(CurrencyPair $currencyPair, ?string $orderId = null, ?string $clientOrderId = null)
    {
        if (empty($orderId) && empty($clientOrderId)) {
            throw new \RuntimeException('One of values orderId or clientOrderId must be specified.');
        }

        parent::__construct($currencyPair, $clientOrderId);

        $this->orderId = $orderId;
    }
}