<?php

namespace Xoptov\BinancePlatform;

use Binance\API;
use Binance\RateLimiter;
use React\EventLoop\Factory;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Xoptov\BinancePlatform\Model\Trade;
use Xoptov\BinancePlatform\Model\Order;
use Xoptov\BinancePlatform\Model\Active;
use Xoptov\BinancePlatform\Model\Account;
use Xoptov\BinancePlatform\Model\Currency;
use Xoptov\BinancePlatform\Model\Commission;
use Xoptov\BinancePlatform\Model\Transaction;
use React\Socket\Connector as SocketConnector;
use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\TimeTrackAbleInterface;
use Xoptov\BinancePlatform\Model\Event\Trade as TradeEvent;
use Xoptov\BinancePlatform\Model\Request\Trade as TradeRequest;

class Platform
{
    /** @var string */
    private static $stream = "wss://stream.binance.com:9443/ws/";

    /** @var bool */
    private static $created = false;

    /** @var int */
    private static $limit = 500;

    /** @var int */
    private static $subscribes = 0;

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

    /**
     * @param int|null $limit
     * @return null|Platform
     */
    public static function create(?int $limit = 500): ?Platform
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

            if (!$this->exchange->hasCurrencyPair($symbol)) {
                throw new \InvalidArgumentException("Specified symbol dose not trade on exchange.");
            }

            $this->tradePair = $this->exchange->getCurrencyPair($symbol);

            // Loading account information.
            $this->loadAccountInfo();

            $this->history = History::create($this->tradePair, $this->api, static::$limit);

            // Loading and calculate position for trade base active.
            $this->loadPosition();

            // Loading open account orders.
            $this->loadOrders();

        } catch (\Exception $e) {
            //TODO: need handle exception.
            return false;
        }

        $this->initialized = true;

        if (function_exists("onInit")) {
            call_user_func("onInit", $this);
        }

        return true;
    }

    public function run(): void
    {
        if (!$this->initialized) {
            throw new \RuntimeException("Platform must be initialized first.");
        }

        $loop = Factory::create();
        $socketConnector = new SocketConnector($loop);
        $clientConnector = new Connector($loop, $socketConnector);

        // TODO: this code need refactoring.
        // Subscription to WebSocket stream.
        $clientConnector(self::$stream . strtolower($this->tradePair) . "@trade")->then(
            function(WebSocket $ws) use ($loop){
                $ws->on("message", function ($data) {
                    $json = json_decode($data, true);
                    $this->handleTrade($json);
                });
                $ws->on("close", function ($code = null, $reason = null) use ($loop) {
                    $loop->stop();
                });
            },
            function($e) use ($loop) {
                $loop->stop();
            }
        );

        $loop->run();
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param int $orderId
     * @return bool
     */
    public function hasOrder(int $orderId): bool
    {
        return $this->account->hasOrder($orderId);
    }

    /**
     * @param string $symbol
     * @return null|CurrencyPair
     */
    public function getCurrencyPair(string $symbol): ?CurrencyPair
    {
        return $this->exchange->getCurrencyPair($symbol);
    }

    /**
     * @param string $symbol
     * @return null|Currency
     */
    public function getCurrency(string $symbol): ?Currency
    {
        return $this->exchange->getCurrency($symbol);
    }

    /**
     * @param string $type
     * @return float
     */
    public function getAccountFee(string $type): float
    {
        return $this->account->getFee($type);
    }

    /**
     * @param int $orderId
     * @return bool
     */
    public function isMyOrder(int $orderId): bool
    {
        return $this->account->hasOrder($orderId);
    }

    /**
     * @param float $volume
     * @param int   $fee
     * @return float
     */
    public function calculateCommissionVolume(float $volume, int $fee): float
    {
        return $volume * ($fee / 100) / 100;
    }

    /**
     * @param TradeRequest $tradeRequest
     */
    public function orderSend(TradeRequest $tradeRequest)
    {
        //TODO: need implement this logic.
    }

    /**
     * Platform constructor.
     */
    private function __construct()
    {
        static::$created = true;
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

                $currencyPair = $this->getCurrencyPair($item["symbol"]);

                if (!$currencyPair) {
                    throw new \RuntimeException("Unknown symbol in order.");
                }

                // Not load not actual orders.
                if (!in_array($item["status"], [Order::STATUS_NEW, Order::STATUS_PARTIALLY_FILLED])) {
                    continue;
                }

                $order = $this->createOrder($currencyPair, $item);

                $this->account->addOrder($order);
            }

            if (isset($item) && key_exists("orderId", $item)) {
                $lastOrderId = $item["orderId"];
            }

        } while (count($result) == static::$limit);
    }

    /**
     * @throws \Exception
     */
    private function loadPosition(): void
    {
        $active = $this->account->getActive($this->tradePair->getBaseCurrency());

        if (!$active) {
            return;
        }

        $active->beginCalculatePosition();

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

        $active->endCalculatePosition();
    }

    /**
     * @param array $data
     */
    private function handleTrade(array $data): void
    {
        $currencyPair = $this->exchange->getCurrencyPair($data['s']);

        if (!$currencyPair) {
            throw new \RuntimeException("Currency with symbol \"{$data['s']}\" not found.");
        }

        $event = new TradeEvent($currencyPair, $data);

        if ($this->isMyOrder($event->getBuyerOrderId())) {
            $this->handlePurchase($event);
        } elseif ($this->isMyOrder($event->getSellerOrderId())) {
            $this->handleSale($event);
        }

        if (function_exists("onTrade")) {
            call_user_func("onTrade", $event, $this);
        }
    }

    /**
     * @param TradeEvent $event
     */
    private function handlePurchase(TradeEvent $event): void
    {
        if ($event->isBuyerMaker()) {
            $fee = $this->getAccountFee(Account::FEE_MAKER);
        } else {
            $fee = $this->getAccountFee(Account::FEE_TAKER);
        }

        $commission = new Commission($event->getBaseCurrency(), $this->calculateCommissionVolume($event->getVolume(), $fee));

        $trade = $this->createTrade($event, Trade::TYPE_BUY, $commission, $event->isBuyerMaker());
        $trade->setOrderId($event->getBuyerOrderId());

        $this->account->purchase($trade);
    }

    /**
     * @param TradeEvent $event
     */
    private function handleSale(TradeEvent $event): void
    {
        if ($event->isBuyerMaker()) {
            $fee = $this->getAccountFee(Account::FEE_TAKER);
        } else {
            $fee = $this->getAccountFee(Account::FEE_MAKER);
        }

        $commission = new Commission($event->getQuoteCurrency(), $this->calculateCommissionVolume($event->getTotal(), $fee));

        $trade = $this->createTrade($event, Trade::TYPE_SELL, $commission, !$event->isBuyerMaker());
        $trade->setOrderId($event->getSellerOrderId());

        $this->account->sale($trade);
    }

    /**
     * @param TradeEvent $event
     * @param string     $type
     * @param Commission $commission
     * @param bool       $isMaker
     * @return Trade
     */
    private function createTrade(TradeEvent $event, string $type, Commission $commission, bool $isMaker): Trade
    {
        return new Trade($event->getTradeId(), $event->getCurrencyPair(), $type, $event->getPrice(), $event->getVolume(), $commission, $isMaker, $event->getTimestamp());
    }

    /**
     * @param CurrencyPair $currencyPair
     * @param array        $data
     * @return Order
     */
    private function createOrder(CurrencyPair $currencyPair, array $data): Order
    {
        return new Order($data["orderId"], $currencyPair, $data["type"], $data["side"], $data["status"],
            $data["price"], $data["origQty"], $data["stopPrice"], $data["executedQty"], $data["icebergQty"],
            $data["time"], $data["updateTime"]
        );
    }
}