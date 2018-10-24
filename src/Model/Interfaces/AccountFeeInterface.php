<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface AccountFeeInterface
{
    const MAKER  = 'maker';
    const TAKER  = 'taker';
    const BUYER  = 'buyer';
    const SELLER = 'seller';

    /**
     * @param string $type
     *
     * @return int
     */
    public function getFee(string $type): int;
}