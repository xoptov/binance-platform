<?php

namespace Xoptov\BinancePlatform\Model\Event;

use Xoptov\BinancePlatform\Model\Currency;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class Trade
{
    private static $number = 0;

    /** @var int */
    private $eventTime;

    /** @var CurrencyPair */
    private $currencyPair;

    /** @var int */
    private $tradeId;

    /** @var float */
    private $price;

    /** @var float */
    private $volume;

    /** @var int */
    private $buyerOrderId;

    /** @var int */
    private $sellerOrderId;

    /** @var int */
    private $timestamp;

    /** @var bool */
    private $buyerMaker;

    /** @var array */
    private static $mapping = [
        "eventTime"     => 'E',
        "tradeId"       => 't',
        "price"         => 'p',
        "volume"        => 'q',
        "buyerOrderId"  => 'b',
        "sellerOrderId" => 'a',
        "timestamp"     => 'T',
        "buyerMaker"    => 'm'
    ];

    /**
     * @param CurrencyPair $currencyPair
     * @param array        $data
     */
    public function __construct(CurrencyPair $currencyPair, array $data)
    {
        $this->currencyPair = $currencyPair;

        foreach (static::$mapping as $property => $field) {
            if (empty($data[$field])) {
                continue;
            }
            $this->{$property} = $data[$field];
        }

        static::$number++;
    }

    /**
     * @return int
     */
    public static function getNumber()
    {
        return static::$number;
    }

    /**
     * @return int
     */
    public function getEventTime(): int
    {
        return $this->eventTime;
    }

    /**
     * @return CurrencyPair
     */
    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }

    /**
     * @return CurrencyPair
     */
    public function getSymbol(): string
    {
        return $this->currencyPair->getSymbol();
    }

    /**
     * @return Currency
     */
    public function getBase(): Currency
    {
        return $this->currencyPair->getBase();
    }

    /**
     * @return Currency
     */
    public function getQuote(): Currency
    {
        return $this->currencyPair->getQuote();
    }

    /**
     * @return int
     */
    public function getTradeId(): int
    {
        return $this->tradeId;
    }

    /**
     * @return mixed
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return int
     */
    public function getBuyerOrderId(): int
    {
        return $this->buyerOrderId;
    }

    /**
     * @return int
     */
    public function getSellerOrderId(): int
    {
        return $this->sellerOrderId;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return bool
     */
    public function isBuyerMaker(): bool
    {
        return $this->buyerMaker;
    }
}