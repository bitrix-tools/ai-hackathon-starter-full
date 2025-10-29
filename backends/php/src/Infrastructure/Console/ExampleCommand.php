<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Bitrix24Core\Bitrix24ServiceBuilderFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:example', description: 'Example CLI command')]
class ExampleCommand extends Command
{
    public function __construct(
        private readonly Bitrix24ServiceBuilderFactory $bitrix24ServiceBuilderFactory,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('ExampleCommand.execute.start');

        $output->writeln(['', 'Hello from example CLI command', '']);
        $output->writeln(sprintf('Load token from storage and execute api-request to portal with command «profile»...'));
        $sb = $this->bitrix24ServiceBuilderFactory->createFromStoredToken();

        $profile = $sb->getMainScope()->main()->getCurrentUserProfile()->getUserProfile();
        $output->writeln([
            '',
            sprintf('id: %s', $profile->ID),
            sprintf('full name: %s %s', $profile->NAME, $profile->LAST_NAME),
        ]);

        $this->logger->info('ExampleCommand.execute.finish');
        return Command::SUCCESS;
    }
}
