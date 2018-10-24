<?php

namespace Xoptov\BinancePlatform\Model\Part;

trait ActionTrait
{
    /** @var mixed */
    protected $id;

    /** @var string */
    protected $type;

    /** @var float */
    protected $volume;

    /** @var int */
    protected $timestamp;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}