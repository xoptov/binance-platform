<?php

namespace Xoptov\BinancePlatform\Model;

class Rate
{
    use RateTrait;

    /**
     * @param float $price
     * @param float $volume
     */
    public function __construct(float $price, float $volume)
    {
        $this->price = $price;
        $this->volume = $volume;
    }
}