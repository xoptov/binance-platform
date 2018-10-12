<?php

namespace Xoptov\BinancePlatform;

use Binance\API;
use Binance\RateLimiter;
use Xoptov\BinancePlatform\Model\Trade;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class TradeHistory
{
    /** @var bool */
    private static $created = false;

    /** @var int */
    private $fromId;

    /** @var API */
    private $client;

    /** @var CurrencyPair */
    private $tradePair;

    /** @var int */
    private $limit;

    /** @var bool End of Stream */
    private $eos = false;

    /**
     * @param RateLimiter  $client
     * @param CurrencyPair $tradePair
     * @param int          $limit
     * @return null|TradeHistory
     */
    public static function create(RateLimiter $client, CurrencyPair $tradePair, int $limit = 500): ?self
    {
        if (self::$created) {
            return null;
        }

        if ($limit > 1000) {
            $limit = 1000;
        } elseif ($limit < 1) {
            $limit = 1;
        }

        return new self($client, $tradePair, $limit);
    }

    /**
     * @return null|array
     * @throws \Exception
     */
    public function get(): ?array
    {
        if ($this->eos) {
            return null;
        }

        if ($this->fromId) {
            $result = $this->client->history($this->tradePair, $this->limit, $this->fromId);
        } else {
            $result = $this->client->history($this->tradePair, $this->limit);
        }

        if (empty($result)) {
            $this->eos = true;

            return null;
        }

        if (count($result) < $this->limit) {
            $this->eos = true;
        }

        $trades = [];

        foreach ($result as $item) {

            if ($item["isBuyer"]) {
                $type = Trade::TYPE_BUY;
            } else {
                $type = Trade::TYPE_SELL;
            }

            $trades[] = new Trade($item["id"], $this->tradePair, $type, $item["price"], $item["qty"], $item["commission"], $item["commissionAsset"], $item["isMaker"], $item["time"]);
            $this->fromId = $item["id"];
        }

        return $trades;
    }

    public function clear(): void
    {
        $this->fromId = null;
        $this->eos = false;
    }

    /**
     * @return bool
     */
    public function isEOS(): bool
    {
        return $this->eos;
    }

    /**
     * @param RateLimiter  $client
     * @param CurrencyPair $tradePair
     * @param int          $limit
     */
    private function __construct(RateLimiter $client, CurrencyPair $tradePair, int $limit)
    {
        $this->client = $client;
        $this->tradePair = $tradePair;
        $this->limit = $limit;
    }
}