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

namespace App\Bitrix24Core\Controller;

use App\Bitrix24Core\Bitrix24ServiceBuilderFactory;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Services\RemoteEventsFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final readonly class AppLifecycleEventController
{
    public function __construct(
        private readonly Bitrix24ServiceBuilderFactory $bitrix24ServiceBuilderFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/api/app-events/', name: 'b24_events', methods: ['POST'])]
    public function process(Request $incomingRequest): Response
    {
        $this->logger->debug('AppLifecycleEventController.process.start', [
            'request' => $incomingRequest->request->all(),
            'baseUrl' => $incomingRequest->getBaseUrl(),
        ]);

        try {
            // check is this request are valid bitrix24 event request?
            if (!RemoteEventsFactory::isCanProcess($incomingRequest)) {
                $this->logger->error('AppLifecycleEventController.process.unknownRequest', [
                    'request' => $incomingRequest->request->all()
                ]);
                throw new InvalidArgumentException(
                    'AppLifecycleEventController controller can process only install or uninstall event requests from bitrix24'
                );
            }

            // todo process incoming events
            // OnApplicationInstall
            // OnApplicationUninstall

            $response = new Response('OK', 200);
            $this->logger->debug('AppLifecycleEventController.process.finish', [
                'response' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
            ]);
            return $response;
        } catch (Throwable $throwable) {
            $this->logger->error('AppLifecycleEventController.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new Response(sprintf('error on bitrix24 event processing: %s', $throwable->getMessage()), 500);
        }
    }
}
