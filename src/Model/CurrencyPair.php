<?php

namespace Xoptov\BinancePlatform\Model;

class CurrencyPair
{
    /** @var int */
    private $id;

    /** @var Currency */
    private $base;

    /** @var Currency */
    private $quote;

    /** @var string */
    private $symbol;

    /**
     * @param int|null $id
     * @param Currency $base
     * @param Currency $quote
     * @param string   $symbol
     */
    public function __construct(?int $id, Currency $base, Currency $quote, string $symbol)
    {
        $this->id = $id;
        $this->base = $base;
        $this->quote = $quote;
        $this->symbol = $symbol;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Currency
     */
    public function getBase(): Currency
    {
        return $this->base;
    }

    /**
     * @return Currency
     */
    public function getQuote(): Currency
    {
        return $this->quote;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }
}