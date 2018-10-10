<?php

namespace Xoptov\BinancePlatform\Repository;

use Xoptov\BinancePlatform\Model\CurrencyPair;

class CurrencyPairRepository extends AbstractRepository
{
    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?CurrencyPair
    {
        $result = $this->findInLoaded(function(CurrencyPair $account) use ($id) {
            return $account->getId() === $id;
        });

        if (!$result) {
            $result = $this->findInDatabase("SELECT * FROM currency_pairs WHERE id = :id", [":id" => $id]);
        }

        return $result;
    }

    /**
     * @param string $symbol
     * @return null|CurrencyPair
     */
    public function findBySymbol(string $symbol): ?CurrencyPair
    {
        $result = $this->findInLoaded(function(CurrencyPair $account) use ($symbol) {
            return $account->getSymbol() === $symbol;
        });

        if (!$result) {
            $result = $this->findInDatabase("SELECT * FROM currency_pairs WHERE symbol = :symbol", [":symbol" => $symbol]);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataClass(): string
    {
        return CurrencyPair::class;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupport(string $class): bool
    {
        return $this->getDataClass() === $class;
    }

    /**
     * @param array $data
     * @return CurrencyPair
     */
    public function hydrate(array $data): CurrencyPair
    {
        //TODO: need implement hydration logic.
    }
}