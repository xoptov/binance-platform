<?php

namespace Xoptov\BinancePlatform\Model;

class Position
{
    /** @var Trade[] */
    private $purchases = array();

    /** @var float */
    private $volume = 0.0;

    /**
     * @return Trade[]
     */
    public function getPurchases(): array
    {
        $purchases = [];

        foreach ($this->purchases as $purchase) {
            $purchases[] = clone $purchase;
        }

        return $purchases;
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    public function hasPurchase(Trade $trade): bool
    {
        /** @var Trade $purchase */
        foreach ($this->purchases as $purchase) {
            if ($purchase->getId() === $trade->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    public function addPurchase(Trade $trade): bool
    {
        if ($this->hasPurchase($trade)) {
            return false;
        }

        $this->volume += $trade->getVolume();
        $this->purchases[] = $trade;

        return true;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @param float $value
     */
    public function decrease(float $value): void
    {
        $this->volume -= $value;
    }
}