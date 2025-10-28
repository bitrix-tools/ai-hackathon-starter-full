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
use Bitrix24\Lib\ApplicationInstallations;
use Bitrix24\Lib\Bitrix24Accounts;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ApiController extends AbstractController
{
    public function __construct(
        private readonly Bitrix24ServiceBuilderFactory $bitrix24ServiceBuilderFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function health(Request $request): JsonResponse
    {
        $this->logger->debug('ApiController.health.start', [
            'request' => $request->request->all(),
            'baseUrl' => $request->getBaseUrl(),
        ]);

        try {
            $response = new JsonResponse([
                'status' => 'healthy',
                'backend' => 'php',
                'timestamp' => time(),
            ], 200);


            $this->logger->debug('ApiController.health.finish', [
                'response' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
            ]);
            return $response;
        } catch (Throwable $throwable) {
            $this->logger->error('ApiController.health.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new JsonResponse([
                'status' => 'failure',
                'backend' => 'php',
                'timestamp' => time(),
            ], 500);
        }
    }
}
