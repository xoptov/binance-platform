<?php

namespace Xoptov\BinancePlatform\Model;

class Position
{
    /**
     * @var Rate[]
     */
    private $purchases = array();

    /**
     * @var float
     */
    private $volume = 0.0;

    /**
     * @param Trade $trade
     *
     * @return bool
     */
    public function purchase(Trade $trade): bool
    {
        if (!$trade->isBuy() || isset($this->purchases[$trade->getId()])) {
            return false;
        }

        $purchase = new Rate($trade->getPrice(), $trade->getActualVolume());

        $this->volume += $purchase->getVolume();
        $this->purchases[$trade->getId()] = $purchase;

        return true;
    }

    /**
     * @param float $value
     */
    public function decrease(float $value): void
    {
        $this->volume -= $value;

        // Recalculating purchases.
        foreach ($this->purchases as $key => $purchase) {

            if ($purchase->getVolume() > $value) {
                $purchase->decreaseVolume($value);
                break;
            }

            if ($purchase->getVolume() == $value) {
                unset($this->purchases[$key]);
                break;
            }

            $value = $value - $purchase->getVolume();
            unset($this->purchases[$key]);
        }
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return float
     */
    public function getAveragePrice(): float
    {
        $totalPrice = 0.0;

        /** @var Rate $purchase */
        foreach ($this->purchases as $purchase) {
            $totalPrice += $purchase->getPrice();
        }

        return $totalPrice / count($this->purchases);
    }

    /**
     * @return float
     */
    public function getWeightedAveragePrice(): float
    {
        $totalPrice = 0.0;
        $totalVolume = 0.0;

        foreach ($this->purchases as $purchase) {
            $totalPrice += $purchase->getTotal();
            $totalVolume += $purchase->getVolume();
        }

        return $totalPrice / $totalVolume;
    }
}