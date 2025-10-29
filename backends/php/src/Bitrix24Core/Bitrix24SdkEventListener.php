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

use Bitrix24\SDK\Application\Contracts\Bitrix24Accounts\Exceptions\MultipleBitrix24AccountsFoundException;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Core\Exceptions\WrongConfigurationException;
use Bitrix24\SDK\Events\AuthTokenRenewedEvent;
use Psr\Log\LoggerInterface;
use Bitrix24\Lib\Bitrix24Accounts;

readonly class Bitrix24SdkEventListener
{
    public function __construct(
        private Bitrix24Accounts\UseCase\RenewAuthToken\Handler $renewAuthTokenHandler,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws WrongConfigurationException
     * @throws InvalidArgumentException
     * @throws MultipleBitrix24AccountsFoundException
     */
    public function onAuthTokenRenewedEventListener(AuthTokenRenewedEvent $authTokenRenewedEvent): void
    {
        $this->logger->info('onAuthTokenRenewedEventListener.start', [
                'member_id' => $authTokenRenewedEvent->getRenewedToken()->memberId
            ]
        );

        // in this example we just renew auth token for one account per portal
        $this->renewAuthTokenHandler->handle(new Bitrix24Accounts\UseCase\RenewAuthToken\Command($authTokenRenewedEvent->getRenewedToken()));

        $this->logger->info('onAuthTokenRenewedEventListener.finish');
    }
}
