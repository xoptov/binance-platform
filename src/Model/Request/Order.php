<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;

abstract class Order
{
    /**
     * @var CurrencyPair
     */
    protected $currencyPair;

    /**
     * @var string|null
     */
    protected $clientOrderId;

    /**
     * Order constructor.
     *
     * @param CurrencyPair $currencyPair
     * @param string|null  $clientOrderId
     */
    public function __construct(CurrencyPair $currencyPair, ?string $clientOrderId = null)
    {
        $this->currencyPair = $currencyPair;
        $this->clientOrderId = $clientOrderId;
    }

    /**
     * @return CurrencyPair
     */
    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }

    /**
     * @return string|null
     */
    public function getClientOrderId(): ?string
    {
        return $this->clientOrderId;
    }
}