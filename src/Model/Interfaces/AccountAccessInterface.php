<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface AccountAccessInterface
{
    const TRADE    = 'trade';
    const WITHDRAW = 'withdraw';
    const DEPOSIT  = 'deposit';

    /**
     * @param string $action
     *
     * @return bool
     */
    public function isCan(string $action): bool;
}