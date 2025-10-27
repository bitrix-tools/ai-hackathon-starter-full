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
use Bitrix24\SDK\Application\ApplicationStatus;
use Bitrix24\SDK\Application\PortalLicenseFamily;
use Bitrix24\SDK\Application\Requests\Events\OnApplicationInstall\OnApplicationInstall;
use Bitrix24\SDK\Application\Requests\Events\OnApplicationUninstall\OnApplicationUninstall;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Services\Main\Common\EventHandlerMetadata;
use Bitrix24\SDK\Services\RemoteEventsFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Throwable;
use Bitrix24\Lib\Bitrix24Accounts;
use Bitrix24\Lib\ApplicationInstallations;
use Bitrix24\Lib\Bitrix24Accounts\ValueObjects\Domain;

class AppLifecycleController extends AbstractController
{
    public function __construct(
        private readonly ApplicationInstallations\UseCase\Install\Handler $installStartHandler,
        private readonly Bitrix24ServiceBuilderFactory $bitrix24ServiceBuilderFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/api/install', name: 'b24_install', methods: ['POST', 'OPTIONS'])]
    public function install(Request $request): Response
    {
        // Handle CORS preflight
        if ($request->getMethod() === 'OPTIONS') {
            return new Response('', 200);
        }

        $this->logger->debug('AppLifecycleController.install.start', [
            'request' => $request->request->all(),
            'baseUrl' => $request->getBaseUrl(),
        ]);

        try {
            // check is this request are valid bitrix24 event request?
            if (!RemoteEventsFactory::isCanProcess($request)) {
                $this->logger->error('AppLifecycleController.install.unknownRequest', [
                    'request' => $request->request->all()
                ]);
                throw new InvalidArgumentException('AppLifecycleController can process only install event requests from bitrix24');
            }

//            //todo check incoming events
//            $b24Event = RemoteEventsFactory::init($this->logger)->createEvent($request, null);
//            $this->logger->debug('AppLifecycleController.install.eventRequest', [
//                'eventClassName' => $b24Event::class,
//                'eventCode' => $b24Event->getEventCode(),
//                'eventPayload' => $b24Event->getEventPayload(),
//            ]);
//
//            if (!$b24Event instanceof OnApplicationInstall) {
//                throw new InvalidArgumentException('LocalAppLifecycleController can process only install events from bitrix24');
//            }
//
//            // now we receive OnApplicationInstall event from Bitrix24
//            $b24ServiceBuilder = $this->bitrix24ServiceBuilderFactory->createFromIncomingEvent($b24Event);
//
//            // get information about portal
//            $b24CurrentUserProfile = $b24ServiceBuilder->getMainScope()->main()->getCurrentUserProfile()->getUserProfile();
//            $b24ApplicationInfo = $b24ServiceBuilder->getMainScope()->main()->getApplicationInfo()->applicationInfo();
//            $b24ApplicationStatus = $b24ApplicationInfo->getStatus();
//            $b24PortalLicenseFamily = $b24ApplicationInfo->LICENSE_FAMILY;
//            $b24PortalUsersCount = $b24ServiceBuilder->getUserScope()->user()->countByFilter([]);
//
//            // You can add additional information into installation fact
//            $contactPersonId = null;
//            $partnerContactPersonId = null;
//            $partnerId = null;
//            $externalId = null;
//            $comment = null;
//            // step 1
//            // save auth tokens into database
//            $this->installStartHandler->handle(
//                new ApplicationInstallations\UseCase\Install\Command(
//                    $b24Event->getAuth()->member_id,
//                    new Domain($b24Event->getAuth()->domain),
//                    $b24Event->getAuth()->authToken,
//                    (int)$b24Event->getEventPayload()['data']['VERSION'],
//                    $b24Event->getAuth()->scope,
//                    $b24CurrentUserProfile->ID,
//                    $b24CurrentUserProfile->ADMIN,
//                    $b24ApplicationStatus,
//                    $b24PortalLicenseFamily,
//                    $b24Event->getAuth()->application_token,
//                    $b24PortalUsersCount,
//                    $contactPersonId,
//                    $partnerContactPersonId,
//                    $partnerId,
//                    $externalId,
//                    $comment
//                )
//            );
//
//            // step 2
//            // register application lifecycle event handlers
//            $eventHandlerUrl = $this->generateUrl(
//                'b24_without_ui_app_events',
//                [],
//                UrlGeneratorInterface::ABSOLUTE_URL
//            );
//            $this->logger->debug('LocalAppLifecycleController.installWithoutUi.startBindEventHandlers', [
//                'eventHandlerUrl' => $eventHandlerUrl
//            ]);
//            $b24ServiceBuilder->getMainScope()->eventManager()->bindEventHandlers(
//                [
//                    // register event handlers for implemented in SDK events
//                    new EventHandlerMetadata(
//                        OnApplicationInstall::CODE,
//                        $eventHandlerUrl,
//                        $b24CurrentUserProfile->ID
//                    ),
//                    new EventHandlerMetadata(
//                        OnApplicationUninstall::CODE,
//                        $eventHandlerUrl,
//                        $b24CurrentUserProfile->ID,
//                    ),
//                ]
//            );
//            $this->logger->debug('InstallController.process.finishBindEventHandlers');
//            // register uninstall event handler


            $response = new Response('OK', 200);
            $this->logger->debug('AppLifecycleController.install.finish', [
                'response' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
            ]);
            return $response;
        } catch (Throwable $throwable) {
            $this->logger->error('AppLifecycleController.install.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new Response(sprintf('error on placement request processing: %s', $throwable->getMessage()), 500);
        }
    }
}
