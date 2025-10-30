<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Bitrix24Core\Bitrix24ServiceBuilderFactory;
use Bitrix24\SDK\Application\Contracts\Bitrix24Accounts\Exceptions\Bitrix24AccountNotFoundException;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Core\Exceptions\UnknownScopeCodeException;
use Bitrix24\SDK\Core\Exceptions\WrongConfigurationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;

#[AsCommand(name: 'app:example', description: 'Example CLI command')]
class ExampleCommand extends Command
{
    public function __construct(
        private readonly Bitrix24ServiceBuilderFactory $bitrix24ServiceBuilderFactory,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    private function parseDomain(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $input = strtolower($input);
        if (str_starts_with($input, 'http://') || str_starts_with($input, 'https://')) {
            $parsed = parse_url($input);
            return $parsed['host'] ?? null;
        }

        return $input;
    }


    /**
     * @throws TransportException
     * @throws WrongConfigurationException
     * @throws InvalidArgumentException
     * @throws Bitrix24AccountNotFoundException
     * @throws UnknownScopeCodeException
     * @throws BaseException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('ExampleCommand.execute.start');

        $output->writeln(['', 'Hello from example CLI command', '']);

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question('Please enter your Bitrix24 domain: ');
        $rawB24Domain = $helper->ask($input, $output, $question);
        if ($rawB24Domain === null) {
            $output->writeln(['', 'error: no domain provided', '']);
            return Command::FAILURE;
        }

        $b24Domain = $this->parseDomain(trim($rawB24Domain));
        if ($b24Domain === null) {
            $output->writeln(['', 'error: invalid domain provided', '']);
            return Command::FAILURE;
        }

        $output->writeln(['', 'Loading Bitrix24 SDK for domain: ' . $b24Domain, '']);
        $sb = $this->bitrix24ServiceBuilderFactory->createFromStoredTokenForDomain($b24Domain);

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
