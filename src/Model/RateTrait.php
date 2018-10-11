<?php

namespace Xoptov\BinancePlatform\Model;

trait RateTrait
{
    /** @var float */
    protected $price;

    /** @var float */
    protected $volume;

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