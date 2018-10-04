<?php

namespace Xoptov\BinancePlatform\Model;

class Transaction
{
	const TYPE_DEPOSIT = 0;
	const TYPE_WITHDRAW = 1;
	
	/** @var Acctive */
	private $active;
	
	/** @var int */
	private $type;
	
	/** @var float */
	private $volume;
	
	/** @var int */
	private $timestamp;
	
	/**
	 * @param Active $active
	 * @param int    $type
	 * @param float  $volume
	 * @param int    $timestamp
	 */
	public function __construct(Active $active, int $type, float $volume, int $timestamp)
	{
		$this->active = $active;
		$this->type = $type;
		$this->volume = $volume;
		$this->timestamp = $timestamp;
	}
	
	/**
	 * @return Active
	 */
	public function getActive(): Active
	{
		return $this->active;
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
	 * @return int
	 */
	public function getTimestamp(): int
	{
		return $this->timestamp;
	}
}