<?php

namespace Xoptov\BinancePlatform;

use PDO;

class Platform
{
    private static $created = false;

    /** @var PDO */
    private $dbh;

    /**
     * @param PDO $dbh
     * @return null|Platform
     */
    public static function create(PDO $dbh)
    {
        if (static::$created) {
            return null;
        }

        return new Platform($dbh);
    }

    /**
     * @param PDO $dbh
     */
    private function __construct(PDO $dbh)
    {
        static::$created = true;

        $this->dbh = $dbh;
    }
}