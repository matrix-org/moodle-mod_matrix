<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
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
        Matrix\Domain\UserId $userId,
        Matrix\Domain\RoomId $roomId
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
        Matrix\Domain\UserId $userId,
        Matrix\Domain\RoomId $roomId
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
        array $content
    ): void {
        $this->httpClient->put(
            \sprintf(
                '/_matrix/client/r0/rooms/%s/state/%s/%s',
                \urlencode($roomId->toString()),
                \urlencode($eventType->toString()),
                \urlencode($stateKey->toString()),
            ),
            $content,
        );
    }

    public function getMembersOfRoom(Matrix\Domain\RoomId $roomId): array
    {
        $members = $this->httpClient->get(\sprintf(
            '/_matrix/client/r0/rooms/%s/members',
            \urlencode($roomId->toString()),
        ));

        $userIds = [];

        foreach ($members['chunk'] as $ev) {
            if ($ev['content'] && $ev['content']['membership']) {
                $membership = $ev['content']['membership'];

                if ('join' === $membership || 'invite' === $membership) {
                    $userIds[] = Matrix\Domain\UserId::fromString($ev['state_key']);
                }
            }
        }

        return $userIds;
    }
}
