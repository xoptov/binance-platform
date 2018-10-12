<?php

namespace Xoptov\BinancePlatform;

use Xoptov\BinancePlatform\Model\Transaction;

class TransactionStorage
{
    /** @var int */
    private $part = 0;

    /** @var Transaction[] */
    private $transactions = array();

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function has(Transaction $transaction): bool
    {
        /** @var Transaction $item */
        foreach ($this->transactions as $item) {
            if ($transaction->isEqual($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function add(Transaction $transaction): bool
    {
        if ($this->has($transaction)) {
            return false;
        }

        $this->transactions[] = $transaction;

        return true;
    }

    public function sort(): void
    {
        // Sorting transactions by timestamp
        usort($this->transactions, function(Transaction $first, Transaction $second) {
            if ($first->getTimestamp() > $second->getTimestamp()) {
                return 1;
            } elseif ($first->getTimestamp() < $second->getTimestamp()) {
                return -1;
            }
            return 0;
        });
    }

    /**
     * @param int      $startTime
     * @param null|int $endTime
     * @return array
     */
    public function get(int $startTime, ?int $endTime = null): array
    {
        if ($this->part) {
            if ($endTime) {
                $part = array_filter($this->transactions, function (Transaction $transaction) use ($startTime, $endTime) {
                    return $transaction->getTimestamp() > $startTime && $transaction->getTimestamp() <= $endTime;
                });
            } else {
                $part = array_filter($this->transactions, function (Transaction $transaction) use ($startTime) {
                    return $transaction->getTimestamp() > $startTime;
                });
            }
        } else {
            if ($endTime) {
                $part = array_filter($this->transactions, function(Transaction $transaction) use ($endTime){
                    return $transaction->getTimestamp() <= $endTime;
                });
            } else {
                $part = $this->transactions;
            }
        }

        $this->part++;

        return $part;
    }

    public function clear(): void
    {
        $this->part = 0;
        $this->transactions = array();
    }
}