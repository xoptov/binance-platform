<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface TransactionTypeInterface
{
    const DEPOSIT  = 'deposit';
    const WITHDRAW = 'withdraw';

    /**
     * @return bool
     */
    public function isDeposit(): bool;

    /**
     * @return bool
     */
    public function isWithdraw(): bool;
}