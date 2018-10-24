<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;

class OrderTakeProfitLimit extends OrderStopLossLimit
{
    /** @var string */
    protected $type = OrderTypeInterface::TAKE_PROFIT_LIMIT;
}