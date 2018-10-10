<?php

namespace Xoptov\BinancePlatform\Repository;

use Xoptov\BinancePlatform\Model\Account;

class AccountRepository extends AbstractRepository
{
    /**
     * @param int $id
     * @return null|Account
     */
    public function find(int $id): ?Account
    {
        $result = $this->findInLoaded(function(Account $account) use ($id) {
            return $account->getId() === $id;
        });

        if (!$result) {
            $result = $this->findInDatabase("SELECT * FROM accounts WHERE id = :id", [":id" => $id]);
        }

        return $result;
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
     * @return Account
     */
    public function hydrate(array $data): Account
    {
        return new Account($data["id"], $data["name"], $data["api_key"], $data["secret"]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataClass(): string
    {
        return Account::class;
    }
}