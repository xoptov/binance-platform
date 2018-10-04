<?php

namespace Xoptov\BinancePlatform\Model;

class Currency
{
    /** @var int */
    private $id;

    /** @var string */
    private $symbol;

    /** @var string */
    private $name;

    /**
     * Currency constructor.
     * @param null|int $id
     * @param string   $symbol
     * @param string   $name
     */
    public function __construct(?int $id, string $symbol, string $name)
    {
        $this->symbol = $symbol;
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}