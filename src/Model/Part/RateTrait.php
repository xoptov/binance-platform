<?php

namespace Xoptov\BinancePlatform\Model\Part;

trait RateTrait
{
    use PriceTrait;

    use VolumeTrait;

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->price * $this->volume;
    }
}