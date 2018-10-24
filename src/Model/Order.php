<?php

namespace Xoptov\BinancePlatform\Model;

use Xoptov\BinancePlatform\Model\Part\TypeTrait;
use Xoptov\BinancePlatform\Model\Part\RateTrait;
use Xoptov\BinancePlatform\Model\Part\IcebergTrait;
use Xoptov\BinancePlatform\Model\Part\StopPriceTrait;
use Xoptov\BinancePlatform\Model\Part\TimeInForceTrait;
use Xoptov\BinancePlatform\Model\Interfaces\OrderTypeInterface;
use Xoptov\BinancePlatform\Model\Interfaces\OrderSideInterface;
use Xoptov\BinancePlatform\Model\Interfaces\TimeInForceInterface;
use Xoptov\BinancePlatform\Model\Interfaces\OrderStatusInterface;

class Order implements OrderSideInterface, OrderTypeInterface, OrderStatusInterface, TimeInForceInterface
{
    use TypeTrait;

    use RateTrait;

    use StopPriceTrait;

    use IcebergTrait;

    use TimeInForceTrait;

	/** @var int */
	private $id;
	
	/** @var CurrencyPair */
	private $currencyPair;
	
	/** @var Trade[] */
	private $trades = array();

    /** @var string */
	private $side;

    /** @var string */
	private $status;

    /** @var float */
    private $executedVolume;

    /** @var int */
	private $createdAt;
	
	/** @var int */
	private $updatedAt;

	/** @var bool */
	private $keepInLock;
	
	/**
	 * @param int          $id
	 * @param CurrencyPair $currencyPair
     * @param string       $type
	 * @param string       $side
	 * @param string       $status
	 * @param float        $price
     * @param float        $volume
     * @param float        $stopPrice
     * @param float        $executedVolume
     * @param float        $icebergVolume
	 * @param int          $createdAt
	 * @param int          $updatedAt
     * @param bool|null    $keepInLock
	 */
	public function __construct(int $id, CurrencyPair $currencyPair, string $type, string $side, string $status, float $price, float $volume, float $stopPrice, float $executedVolume, float $icebergVolume, int $createdAt, int $updatedAt, ?bool $keepInLock = true)
	{
		$this->id = $id;
		$this->currencyPair = $currencyPair;
		$this->type = $type;
		$this->side = $side;
		$this->status = $status;
		$this->price = $price;
		$this->volume = $volume;
		$this->stopPrice = $stopPrice;
		$this->executedVolume = $executedVolume;
		$this->icebergVolume = $icebergVolume;
		$this->createdAt = $createdAt;
		$this->updatedAt = $updatedAt;
		$this->keepInLock = $keepInLock;
	}
	
	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}
	
	/**
	 * @return CurrencyPair
	 */
	public function getCurrencyPair(): CurrencyPair
	{
		return $this->currencyPair;
	}

    /**
     * @return Currency
     */
	public function getBaseCurrency(): Currency
    {
        return $this->currencyPair->getBaseCurrency();
    }

    /**
     * @return Currency
     */
    public function getQuoteCurrency(): Currency
    {
        return $this->currencyPair->getQuoteCurrency();
    }

    /**
     * @return Trade[]
     */
	public function getTrades(): array
    {
        $trades = [];

        foreach ($this->trades as $trade) {
            $trades[] = clone $trade;
        }

        return $trades;
    }

    /**
     * @param Trade $trade
     * @return bool
     * @todo Refactoring with better validation.
     */
    public function fill(Trade $trade): bool
    {
        if ($this->getCurrencyPair() !== $trade->getCurrencyPair()) {
            throw new \RuntimeException('Unsupported currency pair.');
        }

        if ($trade->getType() !== $this->getSide()) {
            throw new \RuntimeException('Unsupported trade operation.');
        }

        if (!$this->addTrade($trade)) {
            return false;
        }

        if ($this->getVolume() == $this->getFilledVolume()) {
            $this->status = OrderStatusInterface::FILLED;
        } else {
            $this->status = OrderStatusInterface::PARTIALLY_FILLED;
        }

        return true;
    }

    /**
     * @return float
     */
    public function getFilledVolume(): float
    {
        $filledVolume = 0.0;

        foreach ($this->trades as $trade) {
            $filledVolume += $trade->getVolume();
        }

        return $filledVolume;
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
	public function getStatus(): string
	{
		return $this->status;
	}

    /**
	 * @return int
	 */
	public function getCreatedAt(): int
	{
		return $this->createdAt;
	}

    /**
	 * @return int
	 */
	public function getUpdatedAt(): int
	{
		return $this->updatedAt;
	}

    /**
     * @return bool
     */
	public function isKeepInLock(): bool
    {
        return $this->keepInLock;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return OrderStatusInterface::NEW === $this->status;
    }

    /**
     * @return bool
     */
	public function isFilled(): bool
    {
        return OrderStatusInterface::FILLED === $this->status;
    }

    /**
     * @return bool
     */
    public function isAsk(): bool
    {
        return OrderSideInterface::SELL === $this->side;
    }

    /**
     * @return bool
     */
    public function isBid(): bool
    {
        return OrderSideInterface::BUY === $this->side;
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    private function hasTrade(Trade $trade): bool
    {
        return isset($this->trades[$trade->getId()]);
    }

    /**
     * @param Trade $trade
     * @return bool
     */
    private function addTrade(Trade $trade): bool
    {
        if ($this->hasTrade($trade)) {
            return false;
        }

        $this->trades[$trade->getId()] = $trade;

        return true;
    }
}