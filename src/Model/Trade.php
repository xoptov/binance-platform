<?php

namespace Xoptov\BinancePlatform\Model;

use Xoptov\BinancePlatform\Model\Part\TypeTrait;
use Xoptov\BinancePlatform\Model\Part\PriceTrait;
use Xoptov\BinancePlatform\Model\Part\ActionTrait;
use Xoptov\BinancePlatform\Model\Interfaces\TradeTypeInterface;

class Trade implements TradeTypeInterface
{
    use PriceTrait;

    use ActionTrait;

    use TypeTrait;

    private static $idPrefix = 'int';

    private static $idCounter = 1;

	/**
     * @var int
     */
	private $orderId;

	/**
     * @var CurrencyPair
     */
	private $currencyPair;

	/**
     * @var Commission
     */
	private $commission;

	/**
     * @var bool
     */
	private $maker;

	/**
     * @param mixed        $id
     * @param CurrencyPair $currencyPair
	 * @param string       $type
	 * @param float        $price
     * @param float        $volume
     * @param Commission   $commission
     * @param bool         $maker
	 * @param int          $timestamp
	 */
	public function __construct($id, CurrencyPair $currencyPair, string $type, float $price, float $volume, Commission $commission, bool $maker, int $timestamp)
	{
	    if (empty($id)) {
	        $this->id = self::generateInternalId();
        } else {
	        $this->id = $id;
        }

		$this->currencyPair = $currencyPair;
		$this->type = $type;
		$this->price = $price;
		$this->volume = $volume;
		$this->commission = $commission;
		$this->maker = $maker;
		$this->timestamp = $timestamp;
	}

    /**
     * @return string
     */
	public static function generateInternalId(): string
    {
        return sprintf("%s%d", self::$idPrefix, self::$idCounter++);
    }

    /**
     * @return int|null
     */
	public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     *
     * @return Trade
     */
    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return CurrencyPair
     */
	public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }

    /**
     * @return Currency
     */
    public function getBaseCurrency(): Currency
    {
        return $this->currencyPair->getBaseCurrency();
    }

    /**
     * @return Currency
     */
    public function getQuoteCurrency(): Currency
    {
        return $this->currencyPair->getQuoteCurrency();
    }

    /**
     * @return float
     */
	public function getTotal(): float
    {
        return $this->price * $this->volume;
    }

    /**
     * @return float
     */
    public function getActualVolume(): float
    {
        if ($this->isBuy()) {
            return $this->volume - $this->commission->getVolume();
        }

        return $this->volume;
    }

    /**
     * @return float
     */
    public function getActualTotal(): float
    {
        if ($this->isSell()) {
            return $this->getTotal() - $this->commission->getVolume();
        }

        return $this->getTotal();
    }

    /**
     * @return bool
     */
    public function isBuy(): bool
    {
        return self::BUY === $this->type;
    }

    /**
     * @return bool
     */
    public function isSell(): bool
    {
        return self::SELL === $this->type;
    }
}