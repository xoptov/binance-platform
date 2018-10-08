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
	
	/** @var string */
	private $type;
	
	/** @var Rate */
	private $rate;
	
	/** @var int */
	private $timestamp;
	
	/**
	 * @param int|null $id
	 * @param Order    $order
	 * @param string   $type
	 * @param Rate     $rate
	 * @param int      $timestamp
	 */
	public function __construct(?int $id, Order $order, string $type, Rate $rate, int $timestamp)
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
	public function getOrder(): Order
	{
		return $this->order;
	}
	
	/**
	 * @return string
	 */
	public function getType(): string
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