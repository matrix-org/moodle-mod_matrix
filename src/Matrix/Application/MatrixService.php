<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Application;

use mod_matrix\Matrix;

final class MatrixService
{
    private $api;
    private $configuration;

    public function __construct(
        Matrix\Application\Api $api,
        Matrix\Application\Configuration $configuration
    ) {
        $this->api = $api;
        $this->configuration = $configuration;
    }

    public function urlForRoom(Matrix\Domain\RoomId $roomId): string
    {
        if ('' !== $this->configuration->elementUrl()) {
            return \sprintf(
                '%s/#/room/%s',
                $this->configuration->elementUrl(),
                $roomId->toString(),
            );
        }

        return \sprintf(
            'https://matrix.to/#/%s',
            $roomId->toString(),
        );
    }

    public function createRoom(
        Matrix\Domain\RoomName $name,
        Matrix\Domain\RoomTopic $topic,
        array $creationContent
    ): Matrix\Domain\RoomId {
        $whoami = $this->api->whoAmI();

        $botPowerLevel = Matrix\Domain\PowerLevel::bot();
        $staffPowerLevel = Matrix\Domain\PowerLevel::staff();
        $redactorPowerLevel = Matrix\Domain\PowerLevel::redactor();

        return $this->api->createRoom([
            'creation_content' => $creationContent,
            'initial_state' => [
                [
                    'content' => [
                        'guest_access' => 'forbidden',
                    ],
                    'state_key' => '',
                    'type' => 'm.room.guest_access',
                ],
            ],
            'name' => $name->toString(),
            'power_level_content_override' => [
                'ban' => $botPowerLevel->toInt(),
                'invite' => $botPowerLevel->toInt(),
                'kick' => $botPowerLevel->toInt(),
                'events' => [
                    'm.room.name' => $botPowerLevel->toInt(),
                    'm.room.power_levels' => $botPowerLevel->toInt(),
                    'm.room.history_visibility' => $staffPowerLevel->toInt(),
                    'm.room.canonical_alias' => $staffPowerLevel->toInt(),
                    'm.room.avatar' => $staffPowerLevel->toInt(),
                    'm.room.tombstone' => $botPowerLevel->toInt(),
                    'm.room.server_acl' => $botPowerLevel->toInt(),
                    'm.room.encryption' => $botPowerLevel->toInt(),
                    'm.room.join_rules' => $botPowerLevel->toInt(),
                    'm.room.guest_access' => $botPowerLevel->toInt(),
                ],
                'events_default' => 0,
                'state_default' => $staffPowerLevel->toInt(),
                'redact' => $redactorPowerLevel->toInt(),
                'users' => [
                    $whoami->toString() => $botPowerLevel->toInt(),
                ],
            ],
            'preset' => 'private_chat',
            'topic' => $topic->toString(),
        ]);
    }

    public function removeRoom(Matrix\Domain\RoomId $roomId): void
    {
        $userIdsOfUsersInRoom = $this->api->listUsers($roomId);

        $userIdOfBot = $this->api->whoAmI();

        foreach ($userIdsOfUsersInRoom->toArray() as $userId) {
            if ($userId->equals($userIdOfBot)) {
                continue;
            }

            $this->api->kickUser(
                $roomId,
                $userId,
            );
        }

        $this->api->kickUser(
            $roomId,
            $userIdOfBot,
        );
    }

    public function synchronizeRoomMembersForRoom(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\UserIdCollection $userIdsOfUsers,
        Matrix\Domain\UserIdCollection $userIdsOfStaff
    ): void {
        $userIdsOfUsersInRoom = $this->api->listUsers($roomId);

        $userIdsOfUsersAndStaff = $userIdsOfUsers->merge($userIdsOfStaff);

        $userIdsOfUsersNotYetInRoom = $userIdsOfUsersAndStaff->diff($userIdsOfUsersInRoom);

        foreach ($userIdsOfUsersNotYetInRoom->toArray() as $userIdOfUserNotYetInRoom) {
            $this->api->inviteUser(
                $roomId,
                $userIdOfUserNotYetInRoom,
            );
        }

        $matrixUserIdOfBot = $this->api->whoAmI();

        $powerLevels = $this->api->getState(
            $roomId,
            Matrix\Domain\EventType::fromString('m.room.power_levels'),
            Matrix\Domain\StateKey::fromString(''),
        );

        $powerLevels['users'] = \array_merge(
            [
                $matrixUserIdOfBot->toString() => Matrix\Domain\PowerLevel::bot()->toInt(),
            ],
            \array_combine(
                \array_map(static function (Matrix\Domain\UserId $userId): string {
                    return $userId->toString();
                }, $userIdsOfUsers->toArray()),
                \array_fill(
                    0,
                    \count($userIdsOfUsers->toArray()),
                    Matrix\Domain\PowerLevel::default()->toInt(),
                ),
            ),
            \array_combine(
                \array_map(static function (Matrix\Domain\UserId $userId): string {
                    return $userId->toString();
                }, $userIdsOfStaff->toArray()),
                \array_fill(
                    0,
                    \count($userIdsOfStaff->toArray()),
                    Matrix\Domain\PowerLevel::staff()->toInt(),
                ),
            ),
        );

        $this->api->setState(
            $roomId,
            Matrix\Domain\EventType::fromString('m.room.power_levels'),
            Matrix\Domain\StateKey::fromString(''),
            $powerLevels,
        );

        $userIdsOfUsersAllowedInRoom = Matrix\Domain\UserIdCollection::fromUserIds($matrixUserIdOfBot)
            ->merge($userIdsOfUsers)
            ->merge($userIdsOfStaff);

        $userIdsOfUsersNotAllowedInRoom = $userIdsOfUsersInRoom->diff($userIdsOfUsersAllowedInRoom);

        foreach ($userIdsOfUsersNotAllowedInRoom->toArray() as $userIdOfUserNotAllowedInRoom) {
            $this->api->kickUser(
                $roomId,
                $userIdOfUserNotAllowedInRoom,
            );
        }
    }
}
