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

    /** @var Active[] */
    private $actives = array();

    /** @var Order[] */
    private $orders = array();

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
     * @param string $symbol
     * @return bool
     */
    public function hasActive(string $symbol): bool
    {
        return isset($this->actives[$symbol]);
    }

    /**
     * @param Active $active
     * @return bool
     */
    public function addActive(Active $active): bool
    {
        if ($this->hasActive($active)) {
            return false;
        }

        $this->actives[$active->getSymbol()] = $active;

        return true;
    }

    /**
     * @param string $symbol
     * @return null|Active
     */
    public function getActive(string $symbol): ?Active
    {
        if ($this->hasActive($symbol)) {
            return $this->actives[$symbol];
        }

        return null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function hasOrder(int $id): bool
    {
        return isset($this->orders[$id]);
    }

    /**
     * @param int $id
     * @return null|Order
     */
    public function getOrder(int $id): ?Order
    {
        if ($this->hasOrder($id)) {
            return $this->orders[$id];
        }

        return null;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function addOrder(Order $order): bool
    {
        if ($this->hasOrder($order->getId())) {
            return false;
        }

        $this->orders[$order->getId()] = $order;

        $active = $this->getActive($order->getBaseCurrency());

        if ($active) {
            $active->addOrder($order);
        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function removeOrder(Order $order): bool
    {
        if (!$this->hasOrder($order->getId())) {
            return false;
        }

        unset($this->orders[$order->getId()]);

        $active = $this->getActive($order->getBaseCurrency());

        if ($active) {
            $active->removeOrder($order);
        }

        return true;
    }

    /**
     * @param Trade $trade
     */
    public function purchase(Trade $trade): void
    {
        $order = $this->getOrder($trade->getOrderId());

        if (!$order) {
            throw new \RuntimeException("Order not found.");
        }

        if (!$order->fill($trade)) {
            return;
        }

        if ($order->isFilled()) {
            $this->removeOrder($order);
        }

        $baseActive = $this->getActive($trade->getBaseCurrency());
        $quoteActive = $this->getActive($trade->getQuoteCurrency());

        if (!$baseActive) {
            $baseActive = new Active($trade->getBaseCurrency());
        }

        if (!$baseActive->trade($trade)) {
            return;
        }

        $quoteActive->decrease($trade->getActualTotal());
    }

    /**
     * @param Trade $trade
     */
    public function sale(Trade $trade): void
    {
        $order = $this->getOrder($trade->getOrderId());

        if (!$order) {
            throw new \RuntimeException("Order not found.");
        }

        if (!$order->fill($trade)) {
            return;
        }

        if ($order->isFilled()) {
            $this->removeOrder($order);
        }

        $baseActive = $this->getActive($trade->getBaseCurrency());
        $quoteActive = $this->getActive($trade->getQuoteCurrency());

        if (!$quoteActive) {
            $quoteActive = new Active($trade->getQuoteCurrency());
        }

        if (!$baseActive->trade($trade)) {
            return;
        }

        $quoteActive->increase($trade->getActualTotal());
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