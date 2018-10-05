<?php

namespace Xoptov\BinancePlatform\Command;

use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AccountListCommand extends Command
{
    /** @var PDO */
    private $dbh;

    /**
     * @param PDO    $dbh
     * @param string $name
     */
    public function __construct(PDO $dbh, $name = null)
    {
        parent::__construct($name);

        $this->dbh = $dbh;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName("account:list")
            ->setDescription("Show existed accounts.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}