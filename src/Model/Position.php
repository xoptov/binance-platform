<?php

namespace Xoptov\BinancePlatform\Model;

class Position
{
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
     * @param Active   $active
     * @param Rate     $rate
     */
    public function __construct(Active $active, Rate $rate)
    {
    	$this->active = $active;
    	$this->rate = $rate;
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