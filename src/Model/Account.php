<?php

namespace Xoptov\BinancePlatform\Model;

class Account
{
    const ACCESS_TRADE    = "trade";
    const ACCESS_WITHDRAW = "withdraw";
    const ACCESS_DEPOSIT  = "deposit";

    const FEE_MAKER = "maker";
    const FEE_TAKER = "taker";
    const FEE_BUYER = "buyer";
    const FEE_SELLER = "seller";

    /** @var array */
    private $access = array(
        self::ACCESS_TRADE    => false,
        self::ACCESS_WITHDRAW => false,
        self::ACCESS_DEPOSIT  => false
    );

    /** @var array */
    private $fees = array(
        self::FEE_MAKER  => 0,
        self::FEE_TAKER  => 0,
        self::FEE_BUYER  => 0,
        self::FEE_SELLER => 0
    );

    /**
     * @param array $access
     * @param array $fees
     */
    public function __construct(array $access, array $fees)
    {
        $this->setAccess($access);
        $this->setFees($fees);
    }

    /**
     * @param string $action
     * @return bool
     */
    public function isCan(string $action): bool
    {
        if (key_exists($action, $this->access)) {
            return $this->access[$action];
        }

        return false;
    }

    /**
     * @param string $type
     * @return int
     */
    public function getFee(string $type): int
    {
        if (key_exists($type, $this->fees)) {
            return $this->fees[$type];
        }

        return 0;
    }

    /**
     * @param array $access
     * @return Account
     */
    private function setAccess(array $access): self
    {
        foreach ($access as $key => $value) {
            if (key_exists($key, $this->access)) {
                $this->access[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @param array $fees
     * @return Account
     */
    private function setFees(array $fees): self
    {
        foreach ($fees as $key => $value) {
            if (key_exists($key, $this->fees)) {
                $this->fees[$key] = $value;
            }
        }

        return $this;
    }
}