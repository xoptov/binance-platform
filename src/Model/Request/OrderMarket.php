<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;

class OrderMarket extends OrderOpen
{
    /** @var string */
    protected $type = OrderTypeInterface::MARKET;
}