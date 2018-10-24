<?php

namespace Xoptov\BinancePlatform\Model\Part;

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