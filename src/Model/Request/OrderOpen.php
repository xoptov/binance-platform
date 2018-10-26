<?php

namespace Xoptov\BinancePlatform\Model\Request;

use Xoptov\BinancePlatform\Model\CurrencyPair;
use Xoptov\BinancePlatform\Model\Part\TypeTrait;
use Xoptov\BinancePlatform\Model\Part\VolumeTrait;
use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;

abstract class OrderOpen extends Order implements OrderTypeInterface
{
    use TypeTrait;

    use VolumeTrait;

    /**
     * @var string
     */
    private $side;

    /**
     * @var string
     */
    private $responseType;

    /**
     * @param CurrencyPair $currencyPair
     * @param string       $side
     * @param float        $volume
     * @param string|null  $clientOrderId
     * @param string|null  $responseType
     */
    public function __construct(CurrencyPair $currencyPair, string $side, float $volume, ?string $clientOrderId = null,
                                ?string $responseType = null)
    {
        parent::__construct($currencyPair, $clientOrderId);

        $this->side = $side;
        $this->volume = $volume;
        $this->responseType = $responseType;
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
    public function getResponseType(): string
    {
        return $this->responseType;
    }
}