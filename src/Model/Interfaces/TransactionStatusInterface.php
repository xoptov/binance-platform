<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface TransactionStatusInterface
{
    const DEPOSIT_PENDING = 0;
    const DEPOSIT_SUCCESS = 1;

    const WITHDRAW_EMAIL_SENT        = 0;
    const WITHDRAW_CANCELED          = 1;
    const WITHDRAW_AWAITING_APPROVAL = 2;
    const WITHDRAW_REJECTED          = 3;
    const WITHDRAW_PROCESSING        = 4;
    const WITHDRAW_FAILURE           = 5;
    const WITHDRAW_COMPLETED         = 6;
}