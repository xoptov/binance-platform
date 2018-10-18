<?php

namespace Xoptov\BinancePlatform\Model;

class Order
{
    use RateTrait;

	const SIDE_BUY  = "BUY";
	const SIDE_SELL = "SELL";

	const STATUS_NEW              = "NEW";
    const STATUS_PARTIALLY_FILLED = "PARTIALLY_FILLED";
    const STATUS_FILLED           = "FILLED";
    const STATUS_CANCELED         = "CANCELED";
    const STATUS_PENDING_CANCEL   = "PENDING_CANCEL";
    const STATUS_REJECTED         = "REJECTED";
    const STATUS_EXPIRED          = "EXPIRED";

    const TYPE_LIMIT             = "LIMIT";
    const TYPE_MARKET            = "MARKET";
    const TYPE_STOP_LOSS         = "STOP_LOSS";
    const TYPE_STOP_LOSS_LIMIT   = "STOP_LOSS_LIMIT";
    const TYPE_TAKE_PROFIT       = "TAKE_PROFIT";
    const TYPE_TAKE_PROFIT_LIMIT = "TAKE_PROFIT_LIMIT";
    const TYPE_LIMIT_MAKER       = "LIMIT_MAKER";

	/** @var int */
	private $id;
	
	/** @var CurrencyPair */
	private $currencyPair;
	
	/** @var Trade[] */
	private $trades = array();

    /** @var string */
    private $type;

    /** @var string */
	private $side;

    /** @var string */
	private $status;

	/** @var float */
    private $stopPrice;

    /** @var float */
    private $executedVolume;

    /** @var float */
    private $icebergVolume;

	/** @var int */
	private $createdAt;
	
	/** @var int */
	private $updatedAt;
	
	/**
	 * @param int          $id
	 * @param CurrencyPair $currencyPair
     * @param string       $type
	 * @param string       $side
	 * @param string       $status
	 * @param float        $price
     * @param float        $volume
     * @param float        $stopPrice
     * @param float        $executedVolume
     * @param float        $icebergVolume
	 * @param int          $createdAt
	 * @param int          $updatedAt
	 */
	public function __construct(int $id, CurrencyPair $currencyPair, string $type, string $side, string $status, float $price, float $volume, float $stopPrice, float $executedVolume, float $icebergVolume, int $createdAt, int $updatedAt)
	{
		$this->id = $id;
		$this->currencyPair = $currencyPair;
		$this->type = $type;
		$this->side = $side;
		$this->status = $status;
		$this->price = $price;
		$this->volume = $volume;
		$this->stopPrice = $stopPrice;
		$this->executedVolume = $executedVolume;
		$this->icebergVolume = $icebergVolume;
		$this->createdAt = $createdAt;
		$this->updatedAt = $updatedAt;
	}
	
	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
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
     * @return Trade[]
     */
	public function getTrades(): array
    {
        $trades = [];

        foreach ($this->trades as $trade) {
            $trades[] = clone $trade;
        }

        return $trades;
    }

    /**
     * @param Trade $trade
     * @return bool
     * @todo Refactoring with better validation.
     */
    public function fill(Trade $trade): bool
    {
        if ($this->getCurrencyPair() !== $trade->getCurrencyPair()) {
            throw new \RuntimeException("Unsupported currency pair.");
        }

        if ($trade->getType() !== $this->getSide()) {
            throw new \RuntimeException("Unsupported trade operation.");
        }

        if (!$this->addTrade($trade)) {
            return false;
        }

        if ($this->getVolume() == $this->getFilledVolume()) {
            $this->status = Order::STATUS_FILLED;
        } else {
            $this->status = Order::STATUS_PARTIALLY_FILLED;
        }

        return true;
    }

    /**
     * @return float
     */
    public function getFilledVolume(): float
    {
        $filledVolume = 0.0;

        foreach ($this->trades as $trade) {
            $filledVolume += $trade->getVolume();
        }

        return $filledVolume;
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    private function hasTrade(Trade $trade): bool
    {
        return isset($this->trades[$trade->getId()]);
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    private function addTrade(Trade $trade): bool
    {
        if ($this->hasTrade($trade)) {
            return false;
        }

        $this->trades[$trade->getId()] = $trade;

        return true;
    }
	
	/**
	 * @return string
	 */
	public function getSide(): string
	{
		return $this->side;
	}
	
	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}
	
	/**
	 * @return int
	 */
	public function getCreatedAt(): int
	{
		return $this->createdAt;
	}
	
	/**
	 * @return int
	 */
	public function getUpdatedAt(): int
	{
		return $this->updatedAt;
	}

    /**
     * @return bool
     */
	public function isFilled(): bool
    {
        return self::STATUS_FILLED === $this->status;
    }

    /**
     * @return bool
     */
    public function isAsk(): bool
    {
        return self::SIDE_SELL === $this->side;
    }

    /**
     * @return bool
     */
    public function isBid(): bool
    {
        return self::SIDE_BUY === $this->side;
    }
}