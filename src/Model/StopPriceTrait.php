<?php

namespace Xoptov\BinancePlatform\Model;

trait StopPriceTrait
{
    /** @var float */
    protected $stopPrice;

    /**
     * @return float
     */
    public function getStopPrice(): float
    {
        return $this->stopPrice;
    }
}