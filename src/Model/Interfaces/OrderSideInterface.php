<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface OrderSideInterface
{
    const BUY  = 'BUY';
    const SELL = 'SELL';

    /**
     * @return string
     */
    public function getSide(): string;

    /**
     * @return bool
     */
    public function isAsk(): bool;

    /**
     * @return bool
     */
    public function isBid(): bool;
}