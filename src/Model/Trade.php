<?php

namespace Xoptov\BinancePlatform\Model;

class Trade
{
	const TYPE_BUY  = "buy";
	const TYPE_SELL = "sell";
	
	/** @var int */
	private $id;
	
	/** @var Order */
	private $order;
	
	/** @var int */
	private $type;
	
	/** @var Rate */
	private $rate;
	
	/** @var int */
	private $timestamp;
	
	/**
	 * @param int|null $id
	 * @param Order    $order
	 * @param int      $type
	 * @param Rate     $rate
	 * @param int      $timestamp
	 */
	public function __construct(?int $id, Order $order, int $type, Rate $rate, int $timestamp)
	{
		$this->id = $id;
		$this->order = $order;
		$this->type = $type;
		$this->rate = $rate;
		$this->timestamp = $timestamp;
	}
	
	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return Order
	 */
	public function getOrider(): Order
	{
		return $this->order;
	}
	
	/**
	 * @return int
	 */
	public function getType(): int
	{
		return $this->type;
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
	public function getTimestamp(): int
	{
		return $this->timestamp;
	}
}