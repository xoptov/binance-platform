<?php

namespace Xoptov\BinancePlatform;

use Binance\API;
use Binance\RateLimiter;
use Xoptov\BinancePlatform\Model\Trade;
use Xoptov\BinancePlatform\Model\Commission;
use Xoptov\BinancePlatform\Model\Transaction;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class History
{
    /** @var bool */
    private static $created = false;

    /** @var int */
    private $limit;

    /** @var API */
    private $api;

    /** @var Transaction[] */
    private $transactions = array();

    /** @var int */
    private $transactionsPart = 0;

    /** @var CurrencyPair */
    private $tradePair;

    /** @var int */
    private $tradeLastId = 1;

    /** @var bool */
    private $tradesEOS = false;

    /**
     * @param CurrencyPair $tradePair
     * @param RateLimiter  $api
     * @param int|null     $limit
     * @return History
     * @throws \Exception
     */
    public static function create(CurrencyPair $tradePair, RateLimiter $api, int $limit): self
    {
        if (self::$created) {
            return null;
        }

        return new self($tradePair, $api, $limit);
    }

    /**
     * @return bool
     */
    public function isTradeEOS(): bool
    {
        return $this->tradesEOS;
    }

    /**
     * @return Trade[]|null
     * @throws \Exception
     */
    public function getTrades(): ?array
    {
        if ($this->tradesEOS) {
            return null;
        }

        if ($this->tradeLastId) {
            $result = $this->api->history($this->tradePair, $this->limit, $this->tradesEOS);
        } else {
            $result = $this->api->history($this->tradePair, $this->limit);
        }

        if (empty($result)) {
            $this->tradesEOS = true;

            return null;
        }

        if (count($result) < $this->limit) {
            $this->tradesEOS = true;
        }

        $trades = [];

        foreach ($result as $item) {

            if ($item["isBuyer"]) {
                $type = Trade::TYPE_BUY;
            } else {
                $type = Trade::TYPE_SELL;
            }

            $currency = $this->tradePair->getCurrency($item["commissionAsset"]);

            if (!$currency) {
                throw new \RuntimeException("Unsupported commission currency in trade.");
            }

            $commission = new Commission($currency, $item["commission"]);

            $trades[] = new Trade($item["id"], $item["orderId"], $this->tradePair, $type, $item["price"], $item["qty"], $commission, $item["isMaker"], $item["time"]);
            $this->tradeLastId = $item["id"];
        }

        return $trades;
    }

    /**
     * @param int      $startTime
     * @param null|int $endTime
     * @return Transaction[]
     */
    public function getTransactions(int $startTime, ?int $endTime = null): array
    {
        if ($this->transactionsPart) {
            if ($endTime) {
                $result = array_filter($this->transactions, function (Transaction $transaction) use ($startTime, $endTime) {
                    return $transaction->getTimestamp() > $startTime && $transaction->getTimestamp() <= $endTime;
                });
            } else {
                $result = array_filter($this->transactions, function (Transaction $transaction) use ($startTime) {
                    return $transaction->getTimestamp() > $startTime;
                });
            }
        } else {
            if ($endTime) {
                $result = array_filter($this->transactions, function(Transaction $transaction) use ($endTime){
                    return $transaction->getTimestamp() <= $endTime;
                });
            } else {
                $result = $this->transactions;
            }
        }

        $this->transactionsPart++;

        return $result;
    }

    public function clear(): void
    {
        $this->transactionsPart = 0;
        $this->tradeLastId = 1;
        $this->tradesEOS = false;
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    private function hasTransaction(Transaction $transaction): bool
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
    private function addTransaction(Transaction $transaction): bool
    {
        if ($this->hasTransaction($transaction)) {
            return false;
        }

        $this->transactions[] = $transaction;

        return true;
    }

    /**
     * @throws \Exception
     */
    private function loadTransactions(): void
    {
        $currency = $this->tradePair->getBase();
        $result = $this->api->depositHistory($currency);

        if (!$result["success"]) {
            throw new \RuntimeException("Can not load deposit history.");
        }

        foreach ($result["depositList"] as $deposit) {
            if (Transaction::STATUS_SUCCESS != $deposit["status"]) {
                continue;
            }
            $transaction = new Transaction($deposit["txId"], $currency, Transaction::TYPE_DEPOSIT, $deposit["amount"], $deposit["insertTime"]);
            $this->addTransaction($transaction);
        }

        $result = $this->api->withdrawHistory($currency);

        if (!$result["success"]) {
            throw new \RuntimeException("Can not load withdraw history.");
        }

        foreach ($result["withdrawList"] as $withdraw) {
            if (Transaction::STATUS_COMPLETED != $withdraw["status"]) {
                continue;
            }

            $transaction = new Transaction($withdraw["txId"], $currency, Transaction::TYPE_WITHDRAW, $withdraw["amount"], $withdraw["applyTime"]);
            $this->addTransaction($transaction);
        }

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
     * @param CurrencyPair $tradePair
     * @param RateLimiter  $api
     * @param int          $limit
     * @throws \Exception
     */
    private function __construct(CurrencyPair $tradePair, RateLimiter $api, int $limit)
    {
        $this->tradePair = $tradePair;
        $this->api = $api;
        $this->limit = $limit;

        $this->loadTransactions();

        self::$created = true;
    }
}