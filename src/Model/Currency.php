<?php

namespace Xoptov\BinancePlatform\Model;

class Currency
{
    /** @var string */
    private $symbol;

    /**
     * @param string $symbol
     */
    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }
}