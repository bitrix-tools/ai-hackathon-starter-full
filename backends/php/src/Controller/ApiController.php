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

    #[Route('/api/getToken', name: 'api_get_token', methods: ['POST'])]
    public function getToken(Request $request): JsonResponse
    {
        $this->logger->debug('ApiController.getToken.start', [
            'request' => $request->request->all(),
            'baseUrl' => $request->getBaseUrl(),
        ]);

        try {
            $response = new JsonResponse([
                //todo add token storage and rotation
                'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30',
            ], 200);


            $this->logger->debug('ApiController.getToken.finish', [
                'response' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
            ]);
            return $response;
        } catch (Throwable $throwable) {
            $this->logger->error('ApiController.getToken.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new JsonResponse(['error' => $throwable->getMessage(),], 500);
        }
    }

    #[Route('/api/list', name: 'api_list', methods: ['GET'])]
    public function getList(Request $request): JsonResponse
    {
        $this->logger->debug('ApiController.getList.start', [
            'request' => $request->request->all(),
            'baseUrl' => $request->getBaseUrl(),
        ]);

        try {
            $response = new JsonResponse([
                'one',
                'two',
                'three',
                'four',
                'five',
            ], 200);


            $this->logger->debug('ApiController.getList.finish', [
                'response' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
            ]);
            return $response;
        } catch (Throwable $throwable) {
            $this->logger->error('ApiController.getList.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new JsonResponse(['error' => $throwable->getMessage(),], 500);
        }
    }

    #[Route('/', name: 'default_route', methods: ['GET'])]
    public function getDefaultRoute(Request $request): JsonResponse
    {
        $this->logger->debug('ApiController.getDefaultRoute.start', [
            'request' => $request->request->all(),
            'baseUrl' => $request->getBaseUrl(),
        ]);

        try {
            $response = new JsonResponse([
                'default route for index page, please use /api/* routes',
            ], 200);

            $this->logger->debug('ApiController.getDefaultRoute.finish', [
                'response' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
            ]);
            return $response;
        } catch (Throwable $throwable) {
            $this->logger->error('ApiController.getDefaultRoute.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new JsonResponse(['error' => $throwable->getMessage(),], 500);
        }
    }

    #[Route('/api/enum', name: 'api_enum', methods: ['GET'])]
    public function getEnum(Request $request): JsonResponse
    {
        $this->logger->debug('ApiController.getEnum.start', [
            'request' => $request->request->all(),
            'baseUrl' => $request->getBaseUrl(),
        ]);

        try {
            $response = new JsonResponse([
                'enum one',
                'enum two',
                'enum three',
            ], 200);


            $this->logger->debug('ApiController.getEnum.finish', [
                'response' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
            ]);
            return $response;
        } catch (Throwable $throwable) {
            $this->logger->error('ApiController.getEnum.error', [
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return new JsonResponse(['error' => $throwable->getMessage(),], 500);
        }
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
