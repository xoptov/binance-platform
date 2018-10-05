<?php

namespace Xoptov\BinancePlatform\Model;

class Rate
{
    /** @var float */
    private $price;

    /** @var float */
    private $volume;

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
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }
}