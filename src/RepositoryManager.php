<?php

namespace Xoptov\BinancePlatform;

use Xoptov\BinancePlatform\Repository\AbstractRepository;

class RepositoryManager
{
    /** @var AbstractRepository[] */
    private $repositories = array();

    /**
     * @param AbstractRepository $repository
     * @return bool
     */
    public function add(AbstractRepository $repository): bool
    {
        if (!isset($this->repositories[$repository->getDataClass()])) {
            $this->repositories[$repository->getDataClass()] = $repository;
            $repository->setRepositoryManager($this);

            return true;
        }

        return false;
    }

    /**
     * @param string $dataClass
     * @return null|AbstractRepository
     */
    public function get(string $dataClass): ?AbstractRepository
    {
        if (isset($this->repositories[$dataClass])) {
            return $this->repositories[$dataClass];
        }

        return null;
    }
}