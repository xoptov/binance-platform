<?php

namespace Xoptov\BinancePlatform;

use Binance\API;
use Binance\RateLimiter;
use Xoptov\BinancePlatform\Model\Trade;
use Xoptov\BinancePlatform\Model\Order;
use Xoptov\BinancePlatform\Model\Active;
use Xoptov\BinancePlatform\Model\Account;
use Xoptov\BinancePlatform\Model\Transaction;
use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\TimeTrackAbleInterface;

class Platform
{
    /** @var bool */
    private static $created = false;

    /** @var int */
    private static $limit = 500;

    /** @var bool */
    private $initialized = false;

    /** @var API */
    private $api;

    /** @var Account */
    private $account;

    /** @var CurrencyPair */
    private $tradePair;

    /** @var Exchange */
    private $exchange;

    /** @var History */
    private $history;

    /** @var Order[] */
    private $orders = array();

    /**
     * @param int|null $limit
     * @return null|Platform
     */
    public static function create(?int $limit = 500)
    {
        if (static::$created) {
            return null;
        }

        if ($limit > 1000) {
            static::$limit = 1000;
        } elseif ($limit < 1) {
            static::$limit = 1;
        } else {
            static::$limit = $limit;
        }

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

        $this->api = new RateLimiter(new API($apiKey, $secret));

        try {

            $this->exchange = Exchange::create($this->api);

            // Loading account information.
            $this->loadAccountInfo();

            if (!$this->exchange->hasCurrencyPair($symbol)) {
                throw new \InvalidArgumentException("Specified symbol dose not exist on exchange.");
            }

            $this->tradePair = $this->exchange->getCurrencyPair($symbol);

            $this->history = History::create($this->tradePair, $this->api, static::$limit);

            // Calculating position for trade base active.
            $this->calculatePosition();

            // Loading open account orders.
            $this->loadOrders();

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
    private function loadAccountInfo(): void
    {
        $result = $this->api->account();

        $access = [
            Account::ACCESS_TRADE    => $result["canTrade"],
            Account::ACCESS_WITHDRAW => $result["canWithdraw"],
            Account::ACCESS_DEPOSIT  => $result["canWithdraw"]
        ];

        $fees = [
            Account::FEE_MAKER  => $result["makerCommission"],
            Account::FEE_TAKER  => $result["takerCommission"],
            Account::FEE_BUYER  => $result["buyerCommission"],
            Account::FEE_SELLER => $result["sellerCommission"]
        ];

        $this->account = new Account($access, $fees);

        foreach ($result["balances"] as $item) {

            $volume = $item["free"] + $item["locked"];

            if ($volume == 0) {
                continue;
            }

            $currency = $this->exchange->getCurrency($item["asset"]);

            if (empty($currency)) {
                throw new \RuntimeException("Asset not found.");
            }

            $active = new Active($currency, $volume);
            $this->account->addActive($active);
        }
    }

    /**
     * @throws \Exception
     */
    private function loadOrders(): void
    {
        $lastOrderId = 1;

        do {
            $result = $this->api->orders($this->tradePair, static::$limit, $lastOrderId);

            foreach ($result as $item) {

                if ($this->hasOrder($item["orderId"])) {
                    continue;
                }

                $currencyPair = $this->exchange->getCurrencyPair($item["symbol"]);

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

            if (isset($item) && key_exists("orderId", $item)) {
                $lastOrderId = $item["orderId"];
            }

        } while (count($result) == static::$limit);
    }

    /**
     * @throws \Exception
     */
    private function calculatePosition(): void
    {
        $active = $this->account->getActive($this->tradePair->getBase());

        if (!$active) {
            return;
        }

        $actualVolume = $active->getVolume();
        $active->flush();

        while ($trades = $this->history->getTrades()) {

            /** @var Trade $first */
            $first = current($trades);

            /** @var Trade $last */
            $last = $trades[count($trades) - 1];

            if ($this->history->isTradeEOS()) {
                $transactions = $this->history->getTransactions($first->getTimestamp());
            } else {
                $transactions = $this->history->getTransactions($first->getTimestamp(), $last->getTimestamp());
            }

            /** @var TimeTrackAbleInterface[] $actions */
            $actions = array_merge($transactions, $trades);

            // Sorting action by time.
            usort($actions, function(TimeTrackAbleInterface $a, TimeTrackAbleInterface $b) {
                if ($a->getTimestamp() > $b->getTimestamp()) {
                    return 1;
                } elseif ($a->getTimestamp() < $b->getTimestamp()) {
                    return -1;
                }
                return 0;
            });

            foreach ($actions as $action) {
                if ($action instanceof Trade) {
                    $active->trade($action);
                } elseif ($action instanceof Transaction) {
                    $active->execute($action);
                }
            }
        }
    }

    private function handleTick(array $message): void
    {
        //TODO: need implement.
    }

    private function handleTrade(array $message): void
    {
        //TODO: need implement.
    }

    private function handleBookEvent(array $message): void
    {
        //TODO: need implement.
    }
}