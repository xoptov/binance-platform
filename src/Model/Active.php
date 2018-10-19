<?php

namespace Xoptov\BinancePlatform\Model;

class Active
{
    /** @var Currency */
    private $currency;

    /** @var Position */
    private $position;

    /** @var array */
    private $supportedTransactionTypes = [Transaction::TYPE_DEPOSIT, Transaction::TYPE_WITHDRAW];

    /** @var array */
    private $supportedTradeTypes = [Trade::TYPE_BUY, Trade::TYPE_SELL];

    /** @var double */
    private $volume;

    /** @var double */
    private $actualVolume = 0.0;

    /** @var Transaction[] */
    private $transactions = array();

    /** @var Trade[] */
    private $trades = array();

    /** @var Order[] */
    private $asks = array();

    /**
     * @param Currency $currency
     * @param float    $volume
     */
    public function __construct(Currency $currency, float $volume = 0.0)
    {
        $this->currency = $currency;
        $this->volume = $volume;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSymbol();
    }

    /**
     * @return array
     */
    public function getSupportedTransactionTypes(): array
    {
        return $this->supportedTransactionTypes;
    }

    /**
     * @return array
     */
    public function getSupportedTradeTypes(): array
    {
        return $this->supportedTradeTypes;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->currency->getSymbol();
    }

    /**
     * @return float
     */
    public function getLockedVolume(): float
    {
        $total = 0.0;

        foreach ($this->asks as $ask) {
            $total += $ask->getVolume();
        }

        return $total;
    }

    /**
     * @return float
     */
    public function getFreeVolume(): float
    {
        return $this->getVolume() - $this->getLockedVolume();
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function hasTransaction(Transaction $transaction): bool
    {
        foreach ($this->transactions as $item) {
            if ($transaction->isEqual($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    public function hasTrade(Trade $trade): bool
    {
        return isset($this->trades[$trade->getId()]);
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function execute(Transaction $transaction): bool
    {
        if (!in_array($transaction->getType(), $this->supportedTransactionTypes)) {
            return false;
        }

        if ($this->hasTransaction($transaction)) {
            return false;
        }

        if ($transaction->isWithdraw()) {
            $this->withdraw($transaction);
        } else {
            $this->deposit($transaction);
        }

        $this->transactions[] = $transaction;

        return true;
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    public function trade(Trade $trade): bool
    {
        if (!in_array($trade->getType(), $this->supportedTradeTypes)) {
            return false;
        }

        if ($this->hasTrade($trade)) {
            return false;
        }

        if ($trade->isSell()) {
            $this->sale($trade);
        } else {
            $this->purchase($trade);
        }

        $this->trades[$trade->getId()] = $trade;

        return true;
    }

    /**
     * @param Active $other
     * @return bool
     */
    public function isEqual(Active $other)
    {
        return $this->getCurrency() === $other->getCurrency();
    }

    /**
     * @return float|null
     */
    public function getAveragePrice(): ?float
    {
        if ($this->position) {
            return $this->position->getAveragePrice();
        }

        return null;
    }

    /**
     * @return float|null
     */
    public function getWeightedAveragePrice(): ?float
    {
        if ($this->position) {
            return $this->position->getWeightedAveragePrice();
        }

        return null;
    }

    /**
     * @param float $value
     */
    public function increase(float $value): void
    {
        $this->volume += $value;
    }

    /**
     * @param float $value
     */
    public function decrease(float $value): void
    {
        if ($this->getVolume() < $value) {
            throw new \RuntimeException("Insufficient funds.");
        }

        $this->volume -= $value;

        if (empty($this->position)) {
            return;
        }

        $this->position->decrease($value);

        if ($this->position->getVolume() <= 0.0) {
            $this->position = null;
        }
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function hasAsk(Order $order): bool
    {
        return isset($this->asks[$order->getId()]);
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function addAsk(Order $order): bool
    {
        if (!$order->isAsk()) {
            return false;
        }

        if ($order->getBaseCurrency() !== $this->currency) {
            return false;
        }

        if ($this->hasAsk($order)) {
            return false;
        }

        $this->asks[$order->getId()] = $order;

        return false;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function removeAsk(Order $order): bool
    {
        if (!$order->isAsk()) {
            return false;
        }

        if (!$this->hasAsk($order)) {
            return false;
        }

        unset($this->asks[$order->getId()]);

        return true;
    }

    public function beginCalculatePosition(): void
    {
        $this->actualVolume = $this->volume;
        $this->volume = 0.0;
    }

    public function endCalculatePosition(): void
    {
        $this->volume = $this->actualVolume;
        $this->volume = 0.0;
    }

    /**
     * @param Transaction $transaction
     */
    private function deposit(Transaction $transaction): void
    {
        $this->increase($transaction->getVolume());
    }

    /**
     * @param Transaction $transaction
     */
    private function withdraw(Transaction $transaction): void
    {
        $this->decrease($transaction->getVolume());
    }

    /**
     * @param Trade $trade
     */
    private function purchase(Trade $trade): void
    {
        if (empty($this->position)) {
            $this->position = new Position();
        }

        $this->position->purchase($trade);
        $this->increase($trade->getActualVolume());
    }

    /**
     * @param Trade $trade
     */
    private function sale(Trade $trade): void
    {
        $this->decrease($trade->getVolume());
    }
}