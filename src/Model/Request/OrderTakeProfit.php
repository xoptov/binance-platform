<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;

class OrderTakeProfit extends OrderStopLoss
{
    /** @var string */
    protected $type = OrderTypeInterface::TAKE_PROFIT;
}