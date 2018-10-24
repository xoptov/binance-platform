<?php

namespace Xoptov\BinancePlatform\Model;

use Xoptov\BinancePlatform\Model\Part\RateTrait;

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

    /**
     * @param float $value
     */
    public function decreaseVolume(float $value): void
    {
        $this->volume -= $value;
    }
}