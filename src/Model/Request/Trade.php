<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\VolumeTrait;
use Xoptov\BinancePlatform\Model\CurrencyPair;

abstract class Trade
{
    use VolumeTrait;

    const ACTION_OPEN   = "open";
    const ACTION_CHECK  = "check";
    const ACTION_REMOVE = "remove";

    /** @var string */
    private $action;

    /** @var CurrencyPair */
    private $currencyPair;

    /** @var string */
    private $side;

    /** @var string */
    private $type;

    /** @var string */
    private $responseType;

    /**
     * @param string       $action
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param string       $type
     * @param float        $volume
     * @param string|null  $responseType
     */
    public function __construct(string $action, CurrencyPair $currencyPair, string $side, string $type, float $volume, ?string $responseType = null)
    {
        $this->action = $action;
        $this->currencyPair = $currencyPair;
        $this->side = $side;
        $this->type = $type;
        $this->volume = $volume;
        $this->responseType = $responseType;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return CurrencyPair
     */
    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }

    /**
     * @return string
     */
    public function getSide(): string
    {
        return $this->side;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getResponseType(): string
    {
        return $this->responseType;
    }
}