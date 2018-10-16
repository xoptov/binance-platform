<?php

namespace Xoptov\BinancePlatform\Model;

class CurrencyPair
{
    const STATUS_TRADING = "TRADING";
    const STATUS_BREAK   = "BREAK";

    /** @var Currency */
    private $base;

    /** @var Currency */
    private $quote;

    /** @var string */
    private $status;

    /** @var array */
    private $orderTypes;

    /** @var bool */
    private $icebergAllowed;

    /** @var array */
    private $filters = array();

    /**
     * @param Currency $base
     * @param Currency $quote
     * @param string   $status
     * @param array    $orderTypes
     * @param bool     $icebergAllowed
     * @param array    $filters
     */
    public function __construct(Currency $base, Currency $quote, string $status, array $orderTypes, bool $icebergAllowed, array $filters)
    {
        $this->base = $base;
        $this->quote = $quote;
        $this->status = $status;
        $this->orderTypes = $orderTypes;
        $this->icebergAllowed = $icebergAllowed;

        $this->setFilters($filters);
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
        return sprintf("%s%s", $this->base->getSymbol(), $this->quote->getSymbol());
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getOrderTypes(): array
    {
        return $this->orderTypes;
    }

    /**
     * @return bool
     */
    public function isIcebergAllowed(): bool
    {
        return $this->icebergAllowed;
    }

    /**
     * @param string $filterType
     * @return bool
     */
    public function hasFilter(string $filterType): bool
    {
        return isset($this->filters[$filterType]);
    }

    /**
     * @param string $filterType
     * @return array|null
     */
    public function getFilter(string $filterType): ?array
    {

    }

    public function getFilterField(string $filterType, string $field)
    {

    }

    /**
     * @return bool
     */
    public function isTrading(): bool
    {
        return self::STATUS_TRADING === $this->status;
    }

    /**
     * @return bool
     */
    public function isBreak(): bool
    {
        return self::STATUS_BREAK === $this->status;
    }


    /**
     * @param string $symbol
     * @return null|Currency
     */
    public function getCurrency(string $symbol): ?Currency
    {
        if ($this->base->getSymbol() === $symbol) {
            return $this->base;
        } elseif ($this->quote->getSymbol() === $symbol) {
            return $this->quote;
        }

        return null;
    }

    /**
     * @param array $filters
     */
    private function setFilters(array $filters): void
    {
        foreach ($filters as $item) {
            if ($this->hasFilter($item["filterType"])) {
                continue;
            }

            $filterType = $item["filterType"];
            $this->filters[$filterType] = [];

            foreach ($item as $field => $value) {
                if ("filterType" === $field) {
                    continue;
                }
                $this->filters[$filterType][$field] = $value;
            }
        }
    }
}