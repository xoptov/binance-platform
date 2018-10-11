<?php

namespace Xoptov\BinancePlatform\Model;

class Active
{
    /** @var Currency */
    private $currency;

    /** @var Position */
    private $position;

    /** @var double */
    private $volume;

    /**
     * @param Currency $currency
     * @param float    $volume
     */
    public function __construct(Currency $currency, float $volume = 0.0)
    {
        $this->currency = $currency;
        $this->volume = $volume;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSymbol();
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @param Position $position
     * @return Active
     */
    public function setPosition(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->currency->getSymbol();
    }
}