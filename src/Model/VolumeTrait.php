<?php

namespace Xoptov\BinancePlatform\Model;

trait VolumeTrait
{
    /** @var float */
    protected $volume;

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }
}