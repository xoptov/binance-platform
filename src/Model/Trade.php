<?php

namespace Xoptov\BinancePlatform\Model;

class Trade implements TimeTrackAbleInterface
{
    use PriceTrait;

    use ActionTrait;

	const TYPE_BUY  = "BUY";
	const TYPE_SELL = "SELL";

	/** @var int */
	private $orderId;

	/** @var CurrencyPair */
	private $currencyPair;

	/** @var Commission */
	private $commission;

	/** @var bool */
	private $maker;

	/**
	 * @param int          $id
     * @param CurrencyPair $currencyPair
	 * @param string       $type
	 * @param float        $price
     * @param float        $volume
     * @param Commission   $commission
     * @param bool         $maker
	 * @param int          $timestamp
	 */
	public function __construct(int $id, CurrencyPair $currencyPair, string $type, float $price, float $volume, Commission $commission, bool $maker, int $timestamp)
	{
		$this->id = $id;
		$this->currencyPair = $currencyPair;
		$this->type = $type;
		$this->price = $price;
		$this->volume = $volume;
		$this->commission = $commission;
		$this->maker = $maker;
		$this->timestamp = $timestamp;
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
        return self::TYPE_BUY === $this->type;
    }

    /**
     * @return bool
     */
    public function isSell(): bool
    {
        return self::TYPE_SELL === $this->type;
    }
}