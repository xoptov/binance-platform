<?php

namespace Xoptov\BinancePlatform\Model\Interfaces;

interface OrderStatusInterface
{
    const NEW              = 'NEW';
    const PARTIALLY_FILLED = 'PARTIALLY_FILLED';
    const FILLED           = 'FILLED';
    const CANCELED         = 'CANCELED';
    const PENDING_CANCEL   = 'PENDING_CANCEL';
    const REJECTED         = 'REJECTED';
    const EXPIRED          = 'EXPIRED';

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return bool
     */
    public function isNew(): bool;

    /**
     * @return bool
     */
    public function isFilled(): bool;
}