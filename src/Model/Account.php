<?php

namespace Xoptov\BinancePlatform\Model;

class Account
{
    /** @var int */
    private $id;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $secret;

    /**
     * @param int|null $id
     * @param string   $apiKey
     * @param string   $secret
     */
    public function __construct(?int $id, string $apiKey, string $secret)
    {
        $this->id = $id;
        $this->apiKey = $apiKey;
        $this->secret = $secret;
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
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }
}