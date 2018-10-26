<?php

namespace Xoptov\BinancePlatform\Model\Event;

use Xoptov\BinancePlatform\Model\Rate;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class Book
{
    const TYPE_UPDATE = 'update';

    /**
     * @var CurrencyPair
     */
    private $currencyPair;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var int
     */
    private $firstUpdateId;

    /**
     * @var int
     */
    private $finalUpdateId;

    /**
     * @var Rate[]
     */
    private $bids = array();

    /**
     * @var Rate[]
     */
    private $asks = array();

    /**
     * @param CurrencyPair $currencyPair
     * @param string       $type
     * @param int          $timestamp
     * @param int          $firstUpdateId
     * @param int          $finalUpdateId
     */
    public function __construct(CurrencyPair $currencyPair, string $type, int $timestamp, int $firstUpdateId,
                                int $finalUpdateId)
    {
        $this->currencyPair = $currencyPair;
        $this->type = $type;
        $this->timestamp = $timestamp;
        $this->firstUpdateId = $firstUpdateId;
        $this->finalUpdateId = $finalUpdateId;
    }

    /**
     * @return CurrencyPair
     */
    public function getCurrencyPair(): CurrencyPair
    {
        return clone $this->currencyPair;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getFirstUpdateId(): int
    {
        return $this->firstUpdateId;
    }

    /**
     * @return int
     */
    public function getFinalUpdateId(): int
    {
        return $this->finalUpdateId;
    }

    /**
     * @param Rate $rate
     */
    public function addBid(Rate $rate): void
    {
        foreach ($this->bids as $key => $bid) {
            if ($bid->getPrice() === $rate->getPrice()) {
                $this->bids[$key] = $rate;
                return;
            }
        }
        $this->bids[] = $rate;
    }

    /**
     * @return Rate[]
     */
    public function getBids(): array
    {
        $bids = [];

        foreach ($this->bids as $bid) {
            $bids[] = clone $bid;
        }

        return $bids;
    }

    /**
     * @param Rate $rate
     */
    public function addAsk(Rate $rate): void
    {
        foreach ($this->asks as $key => $ask) {
            if ($ask->getPrice() === $rate->getPrice()) {
                $this->asks[$key] = $rate;
                return;
            }
        }
        $this->asks[] = $rate;
    }

    /**
     * @return Rate[]
     */
    public function getAsks(): array
    {
        $asks = [];

        foreach ($this->asks as $ask) {
            $asks[] = clone $ask;
        }

        return $asks;
    }
}