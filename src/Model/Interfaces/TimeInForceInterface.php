<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface TimeInForceInterface
{
    const GTC = 'GTC';
    const IOC = 'IOC';
    const FOK = 'FOK';

    /**
     * @return string
     */
    public function getTimeInForce(): string;
}