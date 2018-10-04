<?php

namespace Xoptov\BinancePlatform\Model;

class Active
{
    /** @var int */
    private $id;

    /** @var Account */
    private $account;

    /** @var Currency */
    private $currency;

    /** @var Position[] */
    private $positions = array();

    /** @var double */
    private $volume;

    /**
     * @param int|null $id
     * @param Account  $account
     * @param Currency $currency
     * @param float    $volume
     */
    public function __construct(?int $id, Account $account, Currency $currency, float $volume = 0.0)
    {
        $this->id = $id;
        $this->account = $account;
        $this->currency = $currency;
        $this->volume = $volume;
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
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }
}