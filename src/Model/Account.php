<?php

namespace Xoptov\BinancePlatform\Model;

class Account
{
    /** @var bool */
    private $canTrade;

    /** @var bool */
    private $canWithdraw;

    /** @var bool */
    private $canDeposit;

    /** @var float */
    private $makerCommission;

    /** @var float */
    private $takerCommission;

    /** @var float */
    private $buyerCommission;

    /** @var float */
    private $sellerCommission;

    /**
     * @param bool  $canTrade
     * @param bool  $canWithdraw
     * @param bool  $canDeposit
     * @param float $makerCommission
     * @param float $takerCommission
     * @param float $buyerCommission
     * @param float $sellerCommission
     */
    public function __construct(bool $canTrade, bool $canWithdraw, bool $canDeposit, float $makerCommission, float $takerCommission, float $buyerCommission, float $sellerCommission)
    {
        $this->canTrade = $canTrade;
        $this->canWithdraw = $canWithdraw;
        $this->canDeposit = $canDeposit;
        $this->makerCommission = $makerCommission;
        $this->takerCommission = $takerCommission;
        $this->buyerCommission = $buyerCommission;
        $this->sellerCommission = $sellerCommission;
    }

    /**
     * @return bool
     */
    public function isCanTrade(): bool
    {
        return $this->canTrade;
    }

    /**
     * @return bool
     */
    public function isCanWithdraw(): bool
    {
        return $this->canWithdraw;
    }

    /**
     * @return bool
     */
    public function isCanDeposit(): bool
    {
        return $this->canDeposit;
    }

    /**
     * @return float
     */
    public function getMakerCommission(): float
    {
        return $this->makerCommission;
    }

    /**
     * @return float
     */
    public function getTakerCommission(): float
    {
        return $this->takerCommission;
    }

    /**
     * @return float
     */
    public function getBuyerCommission(): float
    {
        return $this->buyerCommission;
    }

    /**
     * @return float
     */
    public function getSellerCommission(): float
    {
        return $this->sellerCommission;
    }
}