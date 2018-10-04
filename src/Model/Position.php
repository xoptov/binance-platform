<?php

namespace Xoptov\BinancePlatform\Model;

class Position
{
	/** @var int */
    private $id;

    /** @var Active */
    private $active;
    
    /** @var Orders[] */
    private $orders = array();
    
    /** @var Trade[] */
    private $purchases = array();
    
    /** @var Trade[] */
    private $sales = array();
    
    /** @var float */
    private $price;
    
    /** @var float */
    private $volume;
    
    /**
     * @param int|null $id
     * @param Active   $active
     * @param float    $price
     * @param float    $volume
     */
    public function __construct(?int $id, Active $active, float $price = 0.0, float $volume = 0.0)
    {
    	$this->id = $id;
    	$this->active = $active;
    	$this->price = $price;
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
    	return $this->price;
    }
    
    /**
     * @return float
     */
    public function getVolume(): float
    {
    	return $this->volume;
    }
}