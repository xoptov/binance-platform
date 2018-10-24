<?php

namespace Xoptov\BinancePlatform\Model;

use Xoptov\BinancePlatform\Model\Interfaces\TradeTypeInterface;

class Active
{
    /** @var Currency */
    private $currency;

    /** @var Position */
    private $position;

    /** @var array */
    private $supportedTransactions = [Transaction::DEPOSIT, Transaction::WITHDRAW];

    /** @var array */
    private $supportedTrades = [TradeTypeInterface::BUY, TradeTypeInterface::SELL];

    /** @var float */
    private $volume;

    /** @var float */
    private $locked;

    /** @var double */
    private $actualVolume = 0.0;

    /** @var Transaction[] */
    private $transactions = array();

    /** @var Trade[] */
    private $trades = array();

    /** @var Order[] */
    private $orders = array();

    /**
     * @param Currency   $currency
     * @param float      $volume
     * @param float|null $locked
     */
    public function __construct(Currency $currency, float $volume = 0.0, ?float $locked = 0.0)
    {
        $this->currency = $currency;
        $this->volume = $volume;
        $this->locked = $locked;
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
    public function getSupportedTransactions(): array
    {
        return $this->supportedTransactions;
    }

    /**
     * @return array
     */
    public function getSupportedTrades(): array
    {
        return $this->supportedTrades;
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
     * @return float
     */
    public function getLocked(): float
    {
        return $this->locked;
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
    public function getFreeVolume(): float
    {
        return $this->getVolume() - $this->locked;
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
        if (!in_array($transaction->getType(), $this->supportedTransactions)) {
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
        if (!in_array($trade->getType(), $this->supportedTrades)) {
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
            throw new \RuntimeException('Insufficient funds.');
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
     * @param float $value
     */
    public function increaseLock(float $value): void
    {
        $this->locked += $value;
    }

    /**
     * @param float $value
     */
    public function decreaseLock(float $value): void
    {
        $this->locked -= $value;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function hasOrder(Order $order): bool
    {
        return isset($this->orders[$order->getId()]);
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function addOrder(Order $order): bool
    {

        if ($this->hasOrder($order)) {
            return false;
        }

        if ($order->isKeepInLock() && $order->isAsk()) {
            $this->increaseLock($order->getVolume());
        }

        $this->orders[$order->getId()] = $order;

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function removeOrder(Order $order): bool
    {
        if (!$this->hasOrder($order)) {
            return false;
        }

        if ($order->isKeepInLock() && $order->isAsk()) {
            $this->decreaseLock($order->getVolume());
        }

        unset($this->orders[$order->getId()]);

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
        unset($this->actualVolume);
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