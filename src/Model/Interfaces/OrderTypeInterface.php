<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface OrderTypeInterface
{
    const LIMIT             = 'LIMIT';
    const MARKET            = 'MARKET';
    const LIMIT_MAKER       = 'LIMIT_MAKER';
    const STOP_LOSS         = 'STOP_LOSS';
    const STOP_LOSS_LIMIT   = 'STOP_LOSS_LIMIT';
    const TAKE_PROFIT       = 'TAKE_PROFIT';
    const TAKE_PROFIT_LIMIT = 'TAKE_PROFIT_LIMIT';

    /**
     * @return string
     */
    public function getType(): string;
}