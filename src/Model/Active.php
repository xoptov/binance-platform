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

    /** @var Transaction[] */
    private $transactions = array();

    /** @var Trade[] */
    private $trades = array();

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
        /** @var Trade $item */
        foreach ($this->trades as $item) {
            if ($trade->getId() === $item->getId()) {
                return true;
            }
        }

        return false;
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
            $this->sell($trade);
        } else {
            $this->buy($trade);
        }

        $this->trades[] = $trade;

        return true;
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
    private function buy(Trade $trade): void
    {
        if (empty($this->position)) {
            $this->position = new Position();
        }

        $this->position->addPurchase($trade);
        $this->increase($trade->getVolume());
    }

    /**
     * @param Trade $trade
     */
    private function sell(Trade $trade): void
    {
        if ($this->getCurrency() === $trade->getCommissionCurrency()) {
            $value = $trade->getVolume() + $trade->getCommissionVolume();
        } else {
            $value = $trade->getVolume();
        }

        $this->decrease($value);
    }

    /**
     * @param float $value
     */
    private function increase(float $value): void
    {
        $this->volume += $value;
    }

    /**
     * @param float $value
     */
    private function decrease(float $value): void
    {
        if ($this->getVolume() < $value) {
            throw new \RuntimeException("Insufficient funds.");
        }

        if (empty($this->position)) {
            $this->volume -= $value;

            return;
        }

        $this->position->decrease($value);

        if ($this->position->getVolume() <= 0.0) {
            $this->position = null;
        }
    }
}