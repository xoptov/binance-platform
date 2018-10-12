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
	 * @return int
	 */
	public function getSide(): int
	{
		return $this->side;
	}
	
	/**
	 * @return int
	 */
	public function getStatus(): int
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
}