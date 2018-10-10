<?php

namespace Xoptov\BinancePlatform\Command;

use PDO;
use Xoptov\BinancePlatform\Model\Account;
use Xoptov\BinancePlatform\Platform;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xoptov\BinancePlatform\Repository\AccountRepository;
use Xoptov\BinancePlatform\RepositoryManager;

class TradeStartCommand extends Command
{
    const SUCCESS = 0;
    const FILE_NOT_FOUND = 1;

    /** @var PDO */
    private $dbh;

    /**
     * @param PDO  $dbh
     * @param null $name
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
            ->setName("trade:start")
            ->setDescription("This command for start trading with ")
            ->addArgument("account", InputArgument::REQUIRED, "Account id for trading.")
            ->addArgument("symbol", InputArgument::REQUIRED, "Currency pair symbol for trading.")
            ->addArgument("script", InputArgument::OPTIONAL, "Script file of trading algorithm.", "trader.php");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $traderScript = $input->getArgument("script");

        if (!file_exists($traderScript)) {
            $output->writeln("File " . $traderScript . " not found!");

            return self::FILE_NOT_FOUND;
        }

        $repositoryManager = new RepositoryManager();
        $repositoryManager->add(new AccountRepository($this->dbh));

        $accountId = $input->getArgument("account");
        $symbol = $input->getArgument("symbol");

        $platform = Platform::create($this->dbh, $repositoryManager);
        $platform->signIn($accountId);
        $platform->load($symbol);

        return self::SUCCESS;
    }
}