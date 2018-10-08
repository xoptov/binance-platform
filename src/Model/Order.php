<?php

namespace Xoptov\BinancePlatform\Model;

class Order
{
	const SIDE_BID = "bid";
	const SIDE_ASK = "ask";

	const STATUS_NEW  = "new";
    const STATUS_PART = "part";
    const STATUS_DONE = "done";

	/** @var int */
	private $id;
	
	/** @var Account */
	private $account;
	
	/** @var CurrencyPair */
	private $currencyPair;
	
	/** @var Trade[] */
	private $trades = array();
	
	/** @var string */
	private $side;
	
	/** @var string */
	private $status;
	
	/** @var Rate */
	private $rate;
	
	/** @var int */
	private $createdAt;
	
	/** @var int */
	private $updatedAt;
	
	/**
	 * @param int|null     $id
	 * @param Account      $account
	 * @param CurrencyPair $currencyPair
	 * @param int          $side
	 * @param int          $status
	 * @param Rate         $rate
	 * @param int          $createdAt
	 * @param int          $updatedAt
	 */
	public function __construct(?int $id, Account $account, CurrencyPair $currencyPair, int $side, int $status, Rate $rate, int $createdAt, int $updatedAt)
	{
		$this->id = $id;
		$this->account = $account;
		$this->currencyPair = $currencyPair;
		$this->side = $side;
		$this->status = $status;
		$this->rate = $rate;
		$this->createdAt = $createdAt;
		$this->updatedAt = $updatedAt;
	}
	
	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return Account
	 */
	public function getAccount(): Account
	{
		return $this->account;
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
	 * @return float
	 */
	public function getVolume(): float
	{
		return $this->rate->getVolume();
	}
	
	/**
	 * @return float
	 */
	public function getPrice(): float
	{
		return $this->rate->getPrice();
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