<?php

namespace Xoptov\BinancePlatform;

use Binance\API;
use Binance\RateLimiter;
use Xoptov\BinancePlatform\Model\ActionInterface;
use Xoptov\BinancePlatform\Model\Trade;
use Xoptov\BinancePlatform\Model\Order;
use Xoptov\BinancePlatform\Model\Active;
use Xoptov\BinancePlatform\Model\Account;
use Xoptov\BinancePlatform\Model\Currency;
use Xoptov\BinancePlatform\Model\Position;
use Xoptov\BinancePlatform\Model\ActionTrait;
use Xoptov\BinancePlatform\Model\Transaction;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class Platform
{
    /** @var bool */
    private static $created = false;

    /** @var int */
    private static $limit;

    /** @var bool */
    private $initialized = false;

    /** @var RateLimiter */
    private $client;

    /** @var Account */
    private $account;

    /** @var CurrencyPair */
    private $tradePair;

    /** @var Active[] */
    private $actives;

    /** @var Currency[] */
    private $currencies = array();

    /** @var CurrencyPair[] */
    private $currencyPairs = array();

    /** @var Order[] */
    private $orders = array();

    /** @var TransactionStorage */
    private $transactionStorage;

    /** @var TradeHistory */
    private $tradeHistory;

    /**
     * @param int|null $limit
     * @return null|Platform
     */
    public static function create(?int $limit = 500)
    {
        if (static::$created) {
            return null;
        }

        self::$limit = $limit;

        return new self();
    }

    /**
     * @param string $symbol
     * @param string $apiKey
     * @param string $secret
     * @return bool
     */
    public function initialize(string $symbol, string $apiKey, string $secret): bool
    {
        if ($this->initialized) {
            return false;
        }

        $api = new API($apiKey, $secret);
        $this->client = new RateLimiter($api);

        try {
            // Loading account information.
            $this->_loadAccountInfo();

            // Loading exchange information.
            $this->_loadExchangeInfo();

            if (!$this->hasCurrencyPair($symbol)) {
                throw new \InvalidArgumentException("Specified symbol dose not exist on exchange.");
            }

            $this->tradePair = $this->getCurrencyPair($symbol);
            $this->tradeHistory = TradeHistory::create($this->client, $this->tradePair, static::$limit);

            // Loading open account orders.
            $this->_loadOrders();

            // Calculating position for trade base active.
            $this->_calculatePosition();

        } catch (\Exception $e) {
            //TODO: need handle exception.
            return false;
        }

        $this->initialized = true;

        return true;
    }

    public function run()
    {
        if (!$this->initialized) {
            throw new \RuntimeException("Platform must be initialized first.");
        }

        //TODO: start event loop.
    }

    private function __construct()
    {
        static::$created = true;
        $this->transactionStorage = new TransactionStorage();
    }

    /**
     * @param string $symbol
     * @return null|Currency
     */
    private function getCurrency(string $symbol): ?Currency
    {
        if ($this->hasCurrency($symbol)) {
            return $this->currencies[$symbol];
        }

        return null;
    }

    /**
     * @param Currency $currency
     * @return bool
     */
    private function addCurrency(Currency $currency): bool
    {
        if ($this->hasCurrency($currency)) {
            return false;
        }

        $this->currencies[$currency->getSymbol()] = $currency;

        return true;
    }

    /**
     * @param string $symbol
     * @return bool
     */
    private function hasCurrency(string $symbol): bool
    {
        return !empty($this->currencies[$symbol]);
    }

    /**
     * @param string $symbol
     * @return null|CurrencyPair
     */
    private function getCurrencyPair(string $symbol): ?CurrencyPair
    {
        if ($this->hasCurrencyPair($symbol)) {
            return $this->currencyPairs[$symbol];
        }

        return null;
    }

    /**
     * @param string $symbol
     * @return bool
     */
    private function hasCurrencyPair(string $symbol): bool
    {
        return !empty($this->currencyPairs[$symbol]);
    }

    /**
     * @param CurrencyPair $currencyPair
     * @return bool
     */
    private function addCurrencyPair(CurrencyPair $currencyPair): bool
    {
        if ($this->hasCurrencyPair($currencyPair)) {
            return false;
        }

        $this->currencyPairs[$currencyPair->getSymbol()] = $currencyPair;

        return true;
    }

    /**
     * @param string $symbol
     * @return null|Active
     */
    private function getActive(string $symbol): ?Active
    {
        if ($this->hasActive($symbol)) {
            return $this->actives[$symbol];
        }

        return null;
    }

    /**
     * @param string $symbol
     * @return bool
     */
    private function hasActive(string $symbol): bool
    {
        return !empty($this->actives[$symbol]);
    }

    /**
     * @param Active $active
     * @return bool
     */
    private function addActive(Active $active): bool
    {
        if ($this->hasActive($active)) {
            return false;
        }

        $this->actives[$active->getSymbol()] = $active;

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    private function hasOrder(int $id): bool
    {
        /** @var Order $order */
        foreach ($this->orders as $order) {
            if ($order->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $id
     * @return null|Order
     */
    private function getOrder(int $id): ?Order
    {
        /** @var Order $order */
        foreach ($this->orders as $order) {
            if ($order->getId() === $id) {
                return $order;
            }
        }

        return null;
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function addOrder(Order $order): bool
    {
        if ($this->hasOrder($order->getId())) {
            return false;
        }

        $this->orders[] = $order;

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function removeOrder(Order $order): bool
    {
        /**
         * @var int   $key
         * @var Order $order
         */
        foreach ($this->orders as $key => $item) {
            if ($order === $item) {
                unset($this->orders[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    private function _loadAccountInfo(): void
    {
        $result = $this->client->account();

        $this->account = new Account(
            $result["canTrade"],
            $result["canWithdraw"],
            $result["canDeposit"],
            $result["makerCommission"],
            $result["takerCommission"],
            $result["buyerCommission"],
            $result["sellerCommission"]
        );

        foreach ($result["balances"] as $item) {

            if ($this->hasActive($item["asset"])) {
                continue;
            }

            if ($item["free"] == 0 && $item["locked"] == 0) {
                continue;
            }

            $currency = $this->getCurrency($item["asset"]);

            if (!$currency) {
                $currency = new Currency($item["asset"]);
                $this->addCurrency($currency);
            }

            $volume = $item["free"] + $item["locked"];
            $this->addActive(new Active($currency, $volume));
        }
    }

    /**
     * @throws \Exception
     */
    private function _loadExchangeInfo(): void
    {
        $result = $this->client->exchangeInfo();

        foreach ($result["symbols"] as $item) {

            if ($this->hasCurrencyPair($item["symbol"])) {
                continue;
            }

            $base = $this->getCurrency($item["baseAsset"]);

            if (!$base) {
                $base = new Currency($item["baseAsset"]);
                $this->addCurrency($base);
            }

            $quote = $this->getCurrency($item["quoteAsset"]);

            if (!$quote) {
                $quote = new Currency($item["quoteAsset"]);
                $this->addCurrency($quote);
            }

            $currencyPair = new CurrencyPair($base, $quote, $item["status"], $item["orderTypes"], $item["icebergAllowed"]);
            $this->addCurrencyPair($currencyPair);
        }
    }

    /**
     * @throws \Exception
     */
    private function _loadOrders(): void
    {
        $lastOrderId = 1;

        do {
            $result = $this->client->orders($this->tradePair, static::$limit, $lastOrderId);

            foreach ($result as $item) {

                if ($this->hasOrder($item["orderId"])) {
                    continue;
                }

                $currencyPair = $this->getCurrencyPair($item["symbol"]);

                if (!$currencyPair) {
                    throw new \RuntimeException("Unknown symbol in order.");
                }

                if (!in_array($item["status"], [Order::STATUS_NEW, Order::STATUS_PARTIALLY_FILLED])) {
                    continue;
                }

                $order = new Order($item["orderId"], $currencyPair, $item["type"], $item["side"], $item["status"],
                    $item["price"], $item["origQty"], $item["stopPrice"], $item["executedQty"], $item["icebergQty"],
                    $item["time"], $item["updateTime"]
                );

                $this->addOrder($order);
            }

            if (isset($item["orderId"])) {
                $lastOrderId = $item["orderId"];
            }

        } while (count($result) == static::$limit);
    }

    /**
     * @throws \Exception
     */
    private function _calculatePosition(): void
    {
        $active = $this->getActive($this->tradePair->getBase());

        if (!$active || $active->getVolume() <= 0) {
            return;
        }

        $this->_loadDeposits($active);
        $this->_loadWithdrawal($active);

        $this->transactionStorage->sort();

        $position = null;

        while ($trades = $this->tradeHistory->get()) {

            /** @var Trade $first */
            $first = current($trades);

            /** @var Trade $last */
            $last = $trades[count($trades) - 1];

            if ($this->tradeHistory->isEOS()) {
                $transactions = $this->transactionStorage->get($first->getTimestamp());
            } else {
                $transactions = $this->transactionStorage->get($first->getTimestamp(), $last->getTimestamp());
            }

            /** @var ActionTrait[] $actions */
            $actions = array_merge($transactions, $trades);

            // Sorting action by time.
            usort($actions, function(ActionTrait $a, ActionTrait $b) {
                if ($a->getTimestamp() < $b->getTimestamp()) {
                    return 1;
                } elseif ($a->getTimestamp() > $b->getTimestamp()) {
                    return -1;
                }
                return 0;
            });

            foreach ($actions as $action) {

                if (empty($position)) {
                    if ($action instanceof Trade && $action->isBuy()) {
                        $position = new Position($active);
                        $position->trade($action);
                    }
                    continue;
                }

                if ($action instanceof Transaction) {
                    $position->withdraw($action);
                } elseif ($action instanceof Trade) {
                    $position->trade($action);
                } else {
                    continue;
                }

                /** @var Position $position */
                if ($position->isClosed()) {
                    $position = null;
                }
            }
        }

        if (!empty($position)) {
            $active->setPosition($position);
        }

        $this->transactionStorage->clear();
        $this->tradeHistory->clear();
    }

    /**
     * @param Active $active
     * @throws \Exception
     */
    private function _loadDeposits(Active $active): void
    {
        $result = $this->client->depositHistory($active);

        if (!$result["success"]) {
            throw new \RuntimeException("Can not load deposit history.");
        }

        foreach ($result["depositList"] as $deposit) {
            if (Transaction::STATUS_SUCCESS != $deposit["status"]) {
                continue;
            }
            $this->transactionStorage->add(
                new Transaction($deposit["txId"], $active, Transaction::TYPE_DEPOSIT, $deposit["amount"], $deposit["insertTime"])
            );
        }
    }

    /**
     * @param Active $active
     * @throws \Exception
     */
    private function _loadWithdrawal(Active $active): void
    {
        $result = $this->client->withdrawHistory($active);

        if (!$result["success"]) {
            throw new \RuntimeException("Can not load withdraw history.");
        }

        foreach ($result["withdrawList"] as $withdraw) {
            if (Transaction::STATUS_COMPLETED != $withdraw["status"]) {
                continue;
            }
            $this->transactionStorage->add(
                new Transaction($withdraw["txId"], $active, Transaction::TYPE_WITHDRAW, $withdraw["amount"], $withdraw["applyTime"])
            );
        }
    }

    private function _handleTick(array $message): void
    {
        //TODO: need implement.
    }

    private function _handleTrade(array $message): void
    {
        //TODO: need implement.
    }

    private function _handleBookEvent(array $message): void
    {
        //TODO: need implement.
    }
}