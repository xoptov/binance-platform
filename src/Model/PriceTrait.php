<?php

namespace Xoptov\BinancePlatform\Model;

trait PriceTrait
{
    /** @var float */
    protected $price;

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}