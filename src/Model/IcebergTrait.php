<?php

namespace Xoptov\BinancePlatform\Model;

trait IcebergTrait
{
    /** @var float */
    protected $icebergVolume;

    /**
     * @return float
     */
    public function getIcebergVolume(): float
    {
        return $this->icebergVolume;
    }
}