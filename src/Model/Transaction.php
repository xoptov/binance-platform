<?php

namespace Xoptov\BinancePlatform\Model;

use Xoptov\BinancePlatform\Model\Part\ActionTrait;
use Xoptov\BinancePlatform\Model\Interfaces\TimeTrackAbleInterface;
use Xoptov\BinancePlatform\Model\Interfaces\TransactionTypeInterface;

class Transaction implements TimeTrackAbleInterface, TransactionTypeInterface
{
    use ActionTrait;

	/**
     * @var Currency
     */
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
     *
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
        return self::DEPOSIT === $this->type;
    }

    /**
     * @return bool
     */
    public function isWithdraw(): bool
    {
        return self::WITHDRAW === $this->type;
    }
}