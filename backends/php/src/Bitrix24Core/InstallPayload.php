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

use Bitrix24\SDK\Core\Credentials\AuthToken;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use JsonException;

readonly class InstallPayload
{
    public function __construct(
        public string $domain,
        public bool $isHttps,
        public string $lang,
        public string $appSid,
        public AuthToken $authToken,
        public string $memberId,
        public int $b24UserId,
        public string $placementCode,
        public array $placementOptions
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws JsonException
     */
    public static function initFromArray(array $payload): self
    {
        return new self(
            (string)$payload['DOMAIN'],
            $payload['PROTOCOL'] === 1,
            (string)$payload['LANG'],
            (string)$payload['APP_SID'],
            AuthToken::initFromArray(
                [
                    'access_token' => (string)$payload['AUTH_ID'],
                    'refresh_token' => (string)$payload['REFRESH_TOKEN'],
                    'expires' => (string)$payload['AUTH_EXPIRES'],
                ]
            ),
            (string)$payload['member_id'],
            (int)$payload['user_id'],
            (string)$payload['PLACEMENT'],
            $payload['PLACEMENT_OPTIONS']
        );
    }
}
