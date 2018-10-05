<?php

namespace Xoptov\BinancePlatform\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TradeStartCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName("trade:start")
            ->setDescription("This command for start trading with ")
            ->addArgument("symbol", InputArgument::REQUIRED, "Currency pair symbol for trading.")
            ->addArgument("script", InputArgument::OPTIONAL, "Script file of trading algorithm.", "trader.php");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once $input->getArgument("script");


    }
}