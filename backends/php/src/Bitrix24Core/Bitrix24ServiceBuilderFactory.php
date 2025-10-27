<?php

/**
 * This file is part of the b24sdk examples package.
 *
 * © Maksim Mesilov <mesilov.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Bitrix24Core;

use Bitrix24\SDK\Application\Contracts\Bitrix24Accounts\Repository\Bitrix24AccountRepositoryInterface;
use Bitrix24\SDK\Core\Contracts\Events\EventInterface;
use Bitrix24\SDK\Core\Credentials\ApplicationProfile;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Core\Exceptions\UnknownScopeCodeException;
use Bitrix24\SDK\Core\Exceptions\WrongConfigurationException;
use Bitrix24\SDK\Services\ServiceBuilder;
use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class Bitrix24ServiceBuilderFactory
{
    //todo create custom logger for sdk
    private const string LOGGER_NAME = 'b24-php-sdk';

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ParameterBagInterface $parameterBag,
        private Bitrix24AccountRepositoryInterface $bitrix24AccountRepository,
        private LoggerInterface $logger,
    ) {
    }


    /**
     * @throws InvalidArgumentException
     * @throws WrongConfigurationException
     */
    public function createFromIncomingEvent(EventInterface $b24Event): ServiceBuilder
    {
        return new ServiceBuilderFactory($this->eventDispatcher, $this->logger)->init(
            $this->getApplicationProfile(),
            $b24Event->getAuth()->authToken,
            $b24Event->getAuth()->domain
        );
    }

    /**
     * @throws WrongConfigurationException
     * @throws UnknownScopeCodeException
     * @throws InvalidArgumentException
     */
    public function createFromStoredToken(): ServiceBuilder
    {
        $localAppDomain = $this->parameterBag->get('bitrix24sdk.app.local.domain');
        if (empty($localAppDomain)) {
            throw new InvalidArgumentException('localAppDomain is empty');
        }

        $b24Account = $this->bitrix24AccountRepository->findByDomain($localAppDomain);

        return new ServiceBuilderFactory(
            $this->eventDispatcher,
            $this->logger,
        )->init(
            $this->getApplicationProfile(),
            // load auth tokens from a database
            $b24Account->getAuth()->getAccessToken(),
            $b24Account->getAuth()->getDomain()
        );
    }

    /**
     * @throws WrongConfigurationException
     * @throws InvalidArgumentException
     * @throws UnknownScopeCodeException
     */
    private function getApplicationProfile(): ApplicationProfile
    {
        try {
            //todo add validation
            return ApplicationProfile::initFromArray([
                'BITRIX24_PHP_SDK_APPLICATION_CLIENT_ID' => $this->parameterBag->get('bitrix24sdk.app.client_id'),
                'BITRIX24_PHP_SDK_APPLICATION_CLIENT_SECRET' => $this->parameterBag->get('bitrix24sdk.app.client_secret'),
                'BITRIX24_PHP_SDK_APPLICATION_SCOPE' => $this->parameterBag->get('bitrix24sdk.app.scope')
            ]);
        } catch (InvalidArgumentException $invalidArgumentException) {
            $this->logger->error(
                'Bitrix24ServiceBuilderFactory.getApplicationProfile.error',
                [
                    'message' => sprintf('cannot read config from $_ENV: %s', $invalidArgumentException->getMessage()),
                    'trace' => $invalidArgumentException->getTraceAsString()
                ]
            );
            throw $invalidArgumentException;
        }
    }
}
