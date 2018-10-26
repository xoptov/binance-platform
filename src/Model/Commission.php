<?php

namespace Xoptov\BinancePlatform\Model;

class Commission
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var float
     */
    private $volume;

    /**
     * @param Currency $currency
     * @param float    $volume
     */
    public function __construct(Currency $currency, float $volume)
    {
        $this->currency = $currency;
        $this->volume = $volume;
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

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->currency->getSymbol();
    }
}