<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface SymbolStatusInterface
{
    const PRE_TRADING   = 'PRE_TRADING';
    const TRADING       = 'TRADING';
    const POST_TRADING  = 'POST_TRADING';
    const END_OF_DAY    = 'END_OF_DAY';
    const HALT          = 'HALT';
    const AUCTION_MATCH = 'AUCTION_MATCH';
    const BREAK         = 'BREAK';

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return bool
     */
    public function isTrading(): bool;

    /**
     * @return bool
     */
    public function isBreak(): bool;
}