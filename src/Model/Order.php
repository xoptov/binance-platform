<?php

namespace Xoptov\BinancePlatform\Model;

class Order
{
	const SIDE_BID = 0;
	const SIDE_ASK = 1;
	
	/** @var int */
	private $id;
	
	/** @var Account */
	private $account;
	
	/** @var CurrencyPair */
	private $currencyPair;
	
	/** @var Trade[] */
	private $trades = array();
	
	/** @var int */
	private $side;
	
	/** @var int */
	private $status;
	
	/** @var float */
	private $volume;
	
	/** @var float */
	private $price;
	
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
	 * @param float        $volume
	 * @param float        $price
	 * @param int          $createdAt
	 * @param int          $updatedAt
	 */
	public function __construct(?int $id, Account $account, CurrencyPair $currencyPair, int $side, int $status, float $volume, float $price, int $createdAt, int $updatedAt)
	{
		$this->id = $id;
		$this->account = $account;
		$this->currencyPair = $currencyPair;
		$this->side = $side;
		$this->status = $status;
		$this->volume = $volume;
		$this->price = $price;
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
		return $this->volume;
	}
	
	/**
	 * @return float
	 */
	public function getPrice(): float
	{
		return $this->price;
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