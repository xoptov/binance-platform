<?php

namespace Xoptov\BinancePlatform\Model\Part;

trait VolumeTrait
{
    /**
     * @var float
     */
    protected $volume;

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }
}