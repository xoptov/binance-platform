<?php

namespace Xoptov\BinancePlatform\Model;

class Trade
{
	const TYPE_BUY = 0;
	const TYPE_SELL = 1;
	
	/** @var int */
	private $id;
	
	/** @var Order */
	private $order;
	
	/** @var int */
	private $type;
	
	/** @var float */
	private $volume;
	
	/** @var float */
	private $price;
	
	/** @var int */
	private $timestamp;
	
	/**
	 * @param int|null $id
	 * @param Order    $order
	 * @param int      $type
	 * @param float    $volume
	 * @param float    $price
	 * @param int      $timestamp
	 */
	public function __construct(?int $id, Order $order, int $type, float $volume, float $price, int $timestamp)
	{
		$this->id = $id;
		$this->order = $order;
		$this->type = $type;
		$this->volume = $volume;
		$this->price = $price;
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
	public function getTimestamp(): int
	{
		return $this->timestamp;
	}
}