<?php

namespace Xoptov\BinancePlatform\Model;

class Trade
{
    use RateTrait;

    use ActionTrait;

	const TYPE_BUY  = "BUY";
	const TYPE_SELL = "SELL";

	/** @var CurrencyPair */
	private $currencyPair;

	/** @var mixed */
	private $order;

	/** @var Commission */
	private $commission;

	/** @var bool */
	private $maker;

	/**
	 * @param int          $id
     * @param mixed        $order
     * @param CurrencyPair $currencyPair
	 * @param string       $type
	 * @param float        $price
     * @param float        $volume
     * @param Commission   $commission
     * @param bool         $maker
	 * @param int          $timestamp
	 */
	public function __construct(int $id, $order, CurrencyPair $currencyPair, string $type, float $price, float $volume, Commission $commission, bool $maker, int $timestamp)
	{
		$this->id = $id;
		$this->order = $order;
		$this->currencyPair = $currencyPair;
		$this->type = $type;
		$this->price = $price;
		$this->volume = $volume;
		$this->commission = $commission;
		$this->maker = $maker;
		$this->timestamp = $timestamp;
	}

    /**
     * @return CurrencyPair
     */
    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }

    /**
     * @return mixed
     */
	public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     * @return Trade
     */
    public function setOrder($order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Currency
     */
    public function getCommissionCurrency(): Currency
    {
        return $this->commission->getCurrency();
    }

    /**
     * @return string
     */
    public function getCommissionSymbol(): string
    {
        return $this->commission->getSymbol();
    }

    /**
     * @return float
     */
    public function getCommissionVolume(): float
    {
        return $this->commission->getVolume();
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