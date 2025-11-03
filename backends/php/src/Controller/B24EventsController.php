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

namespace App\Controller;

use App\Bitrix24Core\Bitrix24ServiceBuilderFactory;
use App\Service\JwtService;
use Bitrix24\Lib\ApplicationInstallations;
use Bitrix24\Lib\Bitrix24Accounts;
use Bitrix24\SDK\Application\Contracts\Bitrix24Accounts\Entity\Bitrix24AccountStatus;
use Bitrix24\SDK\Application\Contracts\Bitrix24Accounts\Exceptions\Bitrix24AccountNotFoundException;
use Bitrix24\SDK\Application\Contracts\Bitrix24Accounts\Repository\Bitrix24AccountRepositoryInterface;
use Bitrix24\SDK\Services\CRM\Contact\Events\OnCrmContactAdd\OnCrmContactAdd;
use Bitrix24\SDK\Services\RemoteEventsFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class B24EventsController extends AbstractController
{
    public function __construct(
        private readonly RemoteEventsFactory $remoteEventsFactory,
        private readonly Bitrix24AccountRepositoryInterface $bitrix24AccountRepository,
        private readonly Bitrix24ServiceBuilderFactory $bitrix24ServiceBuilderFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('api/custom-b24-events/', name: 'b24_custom_events', methods: ['POST'])]
    public function processEvent(Request $request): JsonResponse
    {
        $this->logger->debug('B24EventsController.processEvent.start', [
            'request' => $request->request->all(),
            'baseUrl' => $request->getBaseUrl(),
        ]);

        try {
            // now we receive event from Bitrix24
            // event payload contatins only one field: entity id and some metadata about event
            // in real production application we need to:
            // - store event in queue and finish this request
            // - async process this event with background worker pool

            // in this example we just process event on the fly
            // DO NOT DO THIS IN REAL PRODUCTION APPLICATIONS
            // sometimes you will receive 1000+ per second

            // walidate incoming request
            if (!RemoteEventsFactory::isCanProcess($request)) {
                $this->logger->error('B24EventsController.processEvent.unknownRequest', [
                    'request' => $request->request->all(),
                    'payload' => $request->getContent()
                ]);
            }

            // create bitrix24 event from request
            $b24Event = $this->remoteEventsFactory->create($request);

            // get local account by member id
            $b24Account = $this->bitrix24AccountRepository->findOneAdminByMemberId($b24Event->getAuth()->member_id);
            if ($b24Account === null) {
                $this->logger->warning('B24EventsController.processEvent.unknownAccount', [
                    'eventMemberId' => $b24Event->getAuth()->member_id
                ]);
                throw new Bitrix24AccountNotFoundException(sprintf('b24 account %s not found', $b24Event->getAuth()->member_id));
            }

            // validate incoming event
            $this->remoteEventsFactory->validate($b24Account, $b24Event);

            // now we have valid event related with bitrix24Account
            switch ($b24Event->getEventCode()) {
                case OnCrmContactAdd::CODE:
                    // do something with event
                    $sb = $this->bitrix24ServiceBuilderFactory->createFromStoredTokenForDomain($b24Account->getDomainUrl());

                    $this->logger->debug('B24EventsController.processEvent.OnCrmContactAdd.start', [
                        'PAYLOAD' => $b24Event->getEventPayload(),
                    ]);

                    $b24Contact = $sb->getCRMScope()->contact()->get((int)$b24Event->getEventPayload()['data']['FIELDS']['ID'])->contact();

                    $this->logger->debug('B24EventsController.processEvent.OnCrmContactAdd.finish', [
                        'b24ContactId' => $b24Contact->ID,
                        'b24ContactName' => $b24Contact->NAME,
                    ]);

                    break;
                default:
                    $this->logger->warning('B24EventsController.processEvent.unknownEvent', [
                        'eventCode' => $b24Event->getEventCode(),
                    ]);
                    break;
            }
            return new JsonResponse(['status' => 'ok'], 200);
        } catch (Throwable $throwable) {
            $this->logger->error('B24EventsController.processEvent.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new JsonResponse(['error' => $throwable->getMessage(),], 500);
        }
    }
}
