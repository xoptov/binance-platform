<?php

namespace Xoptov\BinancePlatform\Model\Part;

trait TimeInForceTrait
{
    /**
     * @var string
     */
    protected $timeInForce;

    /**
     * @return string
     */
    public function getTimeInForce(): string
    {
        return $this->timeInForce;
    }
}