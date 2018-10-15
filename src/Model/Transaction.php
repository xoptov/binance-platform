<?php

namespace Xoptov\BinancePlatform\Model;

class Transaction implements TimeTrackAbleInterface
{
    use ActionTrait;

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

	/** @var Currency */
	private $currency;

	/**
     * @param string   $id
	 * @param Currency $currency
	 * @param string   $type
	 * @param float    $volume
	 * @param int      $timestamp
	 */
	public function __construct(string $id, Currency $currency, string $type, float $volume, int $timestamp)
	{
	    $this->id = $id;
		$this->currency = $currency;
		$this->type = $type;
		$this->volume = $volume;
		$this->timestamp = $timestamp;
	}

	/**
	 * @return Currency
	 */
	public function getCurrency(): Currency
	{
		return $this->currency;
	}

    /**
     * @param Transaction $other
     * @return bool
     */
	public function isEqual(Transaction $other): bool
    {
        return $other->getId() === $this->id && $other->getType() === $this->type;
    }

    /**
     * @return bool
     */
    public function isDeposit(): bool
    {
        return self::TYPE_DEPOSIT === $this->type;
    }

    /**
     * @return bool
     */
    public function isWithdraw(): bool
    {
        return self::TYPE_WITHDRAW === $this->type;
    }
}