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

    /**
     * CurrencyPair constructor.
     * @param int|null $id
     * @param Currency $base
     * @param Currency $quote
     */
    public function __construct(?int $id, Currency $base, Currency $quote)
    {
        $this->id = $id;
        $this->base = $base;
        $this->quote = $quote;
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
}