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
use Bitrix24\SDK\Infrastructure\Console\Commands\SplashScreen;
use Bitrix24\SDK\Services\CRM\Contact\Events\OnCrmContactAdd\OnCrmContactAdd;
use Bitrix24\SDK\Services\Main\Common\EventHandlerMetadata;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Throwable;

use function Symfony\Component\String\b;

use const SIGINT;
use const SIGTERM;

#[AsCommand(name: 'app:example', description: 'Example CLI command')]
class ExampleCommand extends Command
{
    private bool $isShouldStopWork = false;

    public function __construct(
        private readonly Bitrix24ServiceBuilderFactory $bitrix24ServiceBuilderFactory,
        private readonly ParameterBagInterface $parameterBag,
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

    #[\Override]
    public function getSubscribedSignals(): array
    {
        return [
            SIGINT, // Interrupt
            SIGTERM // Terminate
        ];
    }

    #[\Override]
    public function handleSignal(int $signal, int|false $previousExitCode = 0): false|int
    {
        $this->isShouldStopWork = true;

        return parent::handleSignal($signal, $previousExitCode);
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

        $output->writeln(SplashScreen::get());
        $symfonyStyle = new SymfonyStyle($input, $output);

        try {
            $output->writeln(['', 'Hello from example CLI command', '']);

            /** @var QuestionHelper $helper */
            $helper = $this->getHelper('question');
            $question = new Question('Please, enter your Bitrix24 domain: ');
            $rawB24Domain = $helper->ask($input, $output, $question);
            if ($rawB24Domain === null) {
                $symfonyStyle->caution('error: no domain provided');
                return Command::FAILURE;
            }
            $b24Domain = $this->parseDomain(trim($rawB24Domain));
            if ($b24Domain === null) {
                $symfonyStyle->caution('error: no domain provided');
                return Command::FAILURE;
            }

            $symfonyStyle->info(['', 'Loading Bitrix24 SDK for domain: ' . $b24Domain]);
            $sb = $this->bitrix24ServiceBuilderFactory->createFromStoredTokenForDomain($b24Domain);

            $profile = $sb->getMainScope()->main()->getCurrentUserProfile()->getUserProfile();
            $symfonyStyle->writeln([
                '',
                sprintf('Hello,  %s %s (%s)', $profile->NAME, $profile->LAST_NAME, $profile->ID),
                'now, you can use this app to work with Bitrix24 portal'
            ]);

            while (true) {
                // process POSIX signals and gracefully shutdown long-running task
                if ($this->isShouldStopWork) {
                    $symfonyStyle->caution('Process interrupted, try to gracefully shutdown long-running task...');

                    return self::SUCCESS;
                }
                /**
                 * @var QuestionHelper $helper
                 *
                 * method «setCode» override «execute» method for object Command
                 * we use SingleCommandApplication for reduce code in this example
                 */
                // @phpstan-ignore-next-line
                $helper = $this->getHelper('question');
                $question = new ChoiceQuestion(
                    'Please select command',
                    [
                        1 => 'events: get list of registered event handlers',
                        2 => 'events: bind OnCrmContactAdd event handler',
                        3 => 'events: unbind all event handlers',
                        4 => 'add new contact',
                        0 => 'exit🚪'
                    ],
                    null
                );
                $question->setErrorMessage('Menu item «%s» is invalid.');
                $menuItem = $helper->ask($input, $output, $question);
                $output->writeln(sprintf('You have just selected: %s', $menuItem));

                switch ($menuItem) {
                    case 'events: get list of registered event handlers':
                        $eventHandlers = $sb->getMainScope()->event()->get()->getEventHandlers();
                        $table = new Table($output);
                        $table->setHeaders(['Event code', 'Handler URL', 'Auth type', 'is offline']);
                        foreach ($eventHandlers as $eventHandler) {
                            $table->addRow([
                                $eventHandler->event,
                                $eventHandler->handler,
                                $eventHandler->auth_type,
                                $eventHandler->offline,
                            ]);
                        }
                        $table->render();

                        break;
                    case 'events: bind OnCrmContactAdd event handler':
                        // generate event handler url for events
                        $eventHandlerUrl = sprintf('%s/api/custom-b24-events', $this->parameterBag->get('APPLICATION_HOST'));
                        $this->logger->debug('ExampleCommand.execute.startBindEventHandlers', [
                            'eventHandlerUrl' => $eventHandlerUrl
                        ]);

                        $sb->getMainScope()->eventManager()->bindEventHandlers([
                            new EventHandlerMetadata(
                                OnCrmContactAdd::CODE,
                                $eventHandlerUrl,
                                $profile->ID
                            )
                        ]);

                        break;
                    case 'events: unbind all event handlers':
                        $sb->getMainScope()->eventManager()->unbindAllEventHandlers();
                        break;
                    case 'add new contact':
                        $symfonyStyle->writeln(['', 'add new contact']);
                        $addResult = $sb->getCRMScope()->contact()->add([
                            'NAME' => sprintf('test contact name %s', time()),
                        ]);
                        $symfonyStyle->writeln(['', 'contact added, id: ' . $addResult->getId(), '']);

                        break;
                    case 'exit🚪':
                        return Command::SUCCESS;
                }
            }
        } catch (BaseException $exception) {
            $symfonyStyle->caution('Bitrix24 error');
            $symfonyStyle->text(
                [
                    $exception->getMessage(),
                ]
            );
        } catch (Throwable $exception) {
            $symfonyStyle->caution('fatal error');
            $symfonyStyle->text(
                [
                    $exception->getMessage(),
                    $exception->getTraceAsString(),
                ]
            );
        }

        $this->logger->info('ExampleCommand.execute.finish');
        return Command::SUCCESS;
    }
}
