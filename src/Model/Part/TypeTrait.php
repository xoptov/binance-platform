<?php

namespace Xoptov\BinancePlatform\Model\Part;

trait TypeTrait
{
    /** @var string */
    protected $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}