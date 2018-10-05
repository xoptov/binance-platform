<?php

namespace Xoptov\BinancePlatform\Model;

class Position
{
	/** @var int */
    private $id;

    /** @var Active */
    private $active;
    
    /** @var Order[] */
    private $orders = array();
    
    /** @var Trade[] */
    private $purchases = array();
    
    /** @var Trade[] */
    private $sales = array();
    
    /** @var Rate */
    private $rate;

    /**
     * @param int|null $id
     * @param Active   $active
     * @param Rate     $rate
     * @param Trade[]  $purchases
     */
    public function __construct(?int $id, Active $active, Rate $rate, array $purchases)
    {
    	$this->id = $id;
    	$this->active = $active;
    	$this->rate = $rate;
    	$this->purchases = $purchases;
    }
    
    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return float
     */
    public function getPrice(): float
    {
    	return $this->rate->getPrice();
    }
    
    /**
     * @return float
     */
    public function getVolume(): float
    {
    	return $this->rate->getVolume();
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        $orders = [];

        foreach ($this->orders as $order) {
            $orders[] = clone $order;
        }

        return $orders;
    }

    /**
     * @return Trade[]
     */
    public function getPurchases(): array
    {
        $purchases = [];

        foreach ($this->purchases as $purchase) {
            $purchases[] = clone $purchase;
        }

        return $purchases;
    }

    /**
     * @return Trade[]
     */
    public function getSales(): array
    {
        $sales = [];

        foreach ($this->sales as $sale) {
            $sales[] = clone $sale;
        }

        return $sales;
    }
}