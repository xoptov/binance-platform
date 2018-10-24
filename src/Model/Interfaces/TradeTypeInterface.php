<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface TradeTypeInterface
{
    const BUY  = 'BUY';
    const SELL = 'SELL';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return bool
     */
    public function isBuy(): bool;

    /**
     * @return bool
     */
    public function isSell(): bool;
}