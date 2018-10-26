<?php

namespace Xoptov\BinancePlatform\Model\Response;

use Xoptov\BinancePlatform\Model\Order;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class Ack
{
    /**
     * @var CurrencyPair
     */
    private $currencyPair;

    /**
     * @var Order
     */
    private $order;

    /**
     * @param CurrencyPair $currencyPair
     * @param Order        $order
     */
    public function __construct(CurrencyPair $currencyPair, Order $order)
    {
        $this->currencyPair = $currencyPair;
        $this->order = $order;
    }

    /**
     * @return CurrencyPair
     */
    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return clone $this->order;
    }
}