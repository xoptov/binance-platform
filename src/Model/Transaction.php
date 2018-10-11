<?php

namespace Xoptov\BinancePlatform\Model;

class Transaction
{
	const TYPE_DEPOSIT  = "deposit";
	const TYPE_WITHDRAW = "withdraw";

	const STATUS_PENDING = 0;
	const STATUS_SUCCESS = 1;

	const STATUS_EMAIL_SENT = 0;
	const STATUS_CANCELED = 1;
	const STATUS_AWAITING_APPROVAL = 2;
	const STATUS_REJECTED = 3;
	const STATUS_PROCESSING = 4;
	const STATUS_FAILURE = 5;
	const STATUS_COMPLETED = 6;

	/** @var string */
	private $id;

	/** @var Active */
	private $active;

	/** @var string */
	private $type;
	
	/** @var float */
	private $volume;
	
	/** @var int */
	private $timestamp;
	
	/**
     * @param string $id
	 * @param Active $active
	 * @param string $type
	 * @param float  $volume
	 * @param int    $timestamp
	 */
	public function __construct(string $id, Active $active, string $type, float $volume, int $timestamp)
	{
	    $this->id = $id;
		$this->active = $active;
		$this->type = $type;
		$this->volume = $volume;
		$this->timestamp = $timestamp;
	}

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
	public function getType(): string
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

    /**
     * @param Transaction $other
     * @return bool
     */
	public function isEqual(Transaction $other): bool
    {
        return $other->getId() === $this->id && $other->getType() === $this->type;
    }
}