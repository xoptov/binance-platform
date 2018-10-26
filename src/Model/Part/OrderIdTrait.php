<?php

namespace Xoptov\BinancePlatform\Model\Part;

trait OrderIdTrait
{
    /**
     * @var int
     */
    protected $orderId;

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }
}