<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Matrix\Infrastructure;

use mod_matrix\Matrix;

final class HttpClientBasedApi implements Matrix\Application\Api
{
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function whoAmI(): Matrix\Domain\UserId
    {
        $r = $this->httpClient->get('/_matrix/client/r0/account/whoami');

        return Matrix\Domain\UserId::fromString($r['user_id']);
    }

    public function createRoom(array $options): Matrix\Domain\RoomId
    {
        $r = $this->httpClient->post(
            '/_matrix/client/r0/createRoom',
            $options,
        );

        return Matrix\Domain\RoomId::fromString($r['room_id']);
    }

    public function inviteUser(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\UserId $userId
    ): void {
        $this->httpClient->post(
            \sprintf(
                '/_matrix/client/r0/rooms/%s/invite',
                \urlencode($roomId->toString()),
            ),
            [
                'user_id' => $userId->toString(),
            ],
        );
    }

    public function kickUser(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\UserId $userId
    ): void {
        $this->httpClient->post(
            \sprintf(
                '/_matrix/client/r0/rooms/%s/kick',
                \urlencode($roomId->toString()),
            ),
            [
                'user_id' => $userId->toString(),
            ],
        );
    }

    public function getState(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\EventType $eventType,
        Matrix\Domain\StateKey $stateKey
    ) {
        return $this->httpClient->get(\sprintf(
            '/_matrix/client/r0/rooms/%s/state/%s/%s',
            \urlencode($roomId->toString()),
            \urlencode($eventType->toString()),
            \urlencode($stateKey->toString()),
        ));
    }

    public function setState(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\EventType $eventType,
        Matrix\Domain\StateKey $stateKey,
        array $state
    ): void {
        if ($stateKey->toString() === '') {
            $this->httpClient->put(
                \sprintf(
                    '/_matrix/client/r0/rooms/%s/state/%s',
                    \urlencode($roomId->toString()),
                    \urlencode($eventType->toString()),
                ),
                $state,
            );

            return;
        }

        $this->httpClient->put(
            \sprintf(
                '/_matrix/client/r0/rooms/%s/state/%s/%s',
                \urlencode($roomId->toString()),
                \urlencode($eventType->toString()),
                \urlencode($stateKey->toString()),
            ),
            $state,
        );
    }

    public function listUsers(Matrix\Domain\RoomId $roomId): Matrix\Domain\UserIdCollection
    {
        $members = $this->httpClient->get(\sprintf(
            '/_matrix/client/r0/rooms/%s/members',
            \urlencode($roomId->toString()),
        ));

        $memberships = [
            Matrix\Domain\Membership::invite()->toString(),
            Matrix\Domain\Membership::join()->toString(),
        ];

        $userIds = [];

        foreach ($members['chunk'] as $event) {
            if (!\is_array($event)) {
                continue;
            }

            if (!\array_key_exists('content', $event)) {
                continue;
            }

            $content = $event['content'];

            if (!\is_array($content)) {
                continue;
            }

            if (!\array_key_exists('membership', $content)) {
                continue;
            }

            $membership = $content['membership'];

            if (!\in_array($membership, $memberships, true)) {
                continue;
            }

            $userIds[] = Matrix\Domain\UserId::fromString($event['state_key']);
        }

        return Matrix\Domain\UserIdCollection::fromUserIds(...$userIds);
    }
}
