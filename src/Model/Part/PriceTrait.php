<?php

namespace Xoptov\BinancePlatform\Model\Part;

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