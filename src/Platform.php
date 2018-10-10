<?php

namespace Xoptov\BinancePlatform;

use PDO;
use Xoptov\BinancePlatform\Model\Account;
use Xoptov\BinancePlatform\Model\CurrencyPair;

class Platform
{
    private static $created = false;

    /** @var PDO */
    private $dbh;

    /** @var Account */
    private $account;

    /** @var CurrencyPair */
    private $currencyPair;

    /** @var RepositoryManager */
    private $repositoryManager;

    /**
     * @param PDO               $dbh
     * @param RepositoryManager $repositoryManager
     * @return null|Platform
     */
    public static function create(PDO $dbh, RepositoryManager $repositoryManager)
    {
        if (static::$created) {
            return null;
        }

        return new Platform($dbh, $repositoryManager);
    }

    /**
     * @param int $accountId
     * @return bool
     */
    public function signIn(int $accountId): bool
    {
        $account = $this->repositoryManager->get(Account::class)->find($accountId);

        if ($account) {
            $this->account = $account;

            return true;
        }

        return false;
    }

    /**
     * @param string $symbol
     */
    public function load(string $symbol)
    {
        $currencyPair = $this->repositoryManager->get(CurrencyPair::class)->findBySymbol($symbol);
    }

    /**
     * @param PDO               $dbh
     * @param RepositoryManager $repositoryManager
     */
    private function __construct(PDO $dbh, RepositoryManager $repositoryManager)
    {
        static::$created = true;

        $this->dbh = $dbh;
        $this->repositoryManager = $repositoryManager;
    }
}