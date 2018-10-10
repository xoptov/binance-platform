<?php

namespace Xoptov\BinancePlatform\Repository;

use PDO;
use Xoptov\BinancePlatform\RepositoryManager;

abstract class AbstractRepository
{
    /** @var PDO */
    private $dbh;

    /** @var array */
    private $loaded = array();

    /** @var RepositoryManager */
    private $repositoryManager;

    /**
     * @param PDO $dbh
     */
    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * @return string
     */
    abstract public function getDataClass(): string;

    /**
     * @param int $id
     * @return mixed
     */
    abstract public function find(int $id);

    /**
     * @param string $class
     * @return bool
     */
    abstract public function isSupport(string $class): bool;

    /**
     * @param array $data
     * @return mixed
     */
    abstract public function hydrate(array $data);

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->dbh;
    }

    /**
     * @return array
     */
    public function getLoaded(): array
    {
        return $this->loaded;
    }

    /**
     * @param RepositoryManager $repositoryManager
     */
    public function setRepositoryManager(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * @param $object
     */
    public function addLoaded($object)
    {
        $hash = spl_object_hash($object);
        $this->loaded[$hash] = $object;
    }

    /**
     * @param string $query
     * @param array  $binding
     * @return mixed|null
     */
    protected function findInDatabase(string $query, array $binding)
    {
        $stmt = $this->getConnection()->prepare($query);

        if (!$stmt->execute($binding)) {
            throw new \PDOException("Query executed with code: " . $stmt->errorCode());
        }

        if ($data = $stmt->fetch()) {
            $object = $this->hydrate($data);
            $this->addLoaded($object);

            return $object;
        }

        return null;
    }

    /**
     * @param callable $callback
     * @return mixed|null
     */
    protected function findInLoaded(callable $callback)
    {
        $result = array_filter($this->getLoaded(), $callback);

        if (empty($result)) {
            return null;
        }

        return current($result);
    }
}