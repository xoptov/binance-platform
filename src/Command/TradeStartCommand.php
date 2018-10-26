<?php

namespace Xoptov\BinancePlatform\Command;

use Xoptov\BinancePlatform\Platform;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TradeStartCommand extends Command
{
    const SUCCESS = 0;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('This command for start trading with ')
            ->addArgument('symbol', InputArgument::REQUIRED, 'Trading symbol for trade.')
            ->addArgument('apiKey', InputArgument::REQUIRED, 'Account api key for connecting to exchange.')
            ->addArgument('secret', InputArgument::REQUIRED, 'Secret for connecting to exchange.')
            ->addArgument('script', InputArgument::OPTIONAL, 'Script file of trading algorithm.', 'trader.php');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $script = $input->getArgument('script');

        if (file_exists($script)) {
            require_once $script;
        }

        $symbol = $input->getArgument('symbol');
        $apiKey = $input->getArgument('apiKey');
        $secret = $input->getArgument('secret');

        $platform = Platform::create();
        $platform->initialize($symbol, $apiKey, $secret);
        $platform->run();

        return self::SUCCESS;
    }
}