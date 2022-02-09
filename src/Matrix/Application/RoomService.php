<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Application;

use mod_matrix\Matrix;

final class RoomService
{
    private $api;

    public function __construct(Matrix\Application\Api $api)
    {
        $this->api = $api;
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
                // https://spec.matrix.org/latest/client-server-api/#mroomencryption
                [
                    'content' => [
                        'algorithm' => 'm.megolm.v1.aes-sha2',
                    ],
                    'state_key' => '',
                    'type' => 'm.room.encryption',
                ],
                // https://spec.matrix.org/latest/client-server-api/#mroomguest_access
                [
                    'content' => [
                        'guest_access' => 'forbidden',
                    ],
                    'state_key' => '',
                    'type' => 'm.room.guest_access',
                ],
                // https://spec.matrix.org/latest/client-server-api/#mroomhistory_visibility
                [
                    'content' => [
                        'history_visibility' => 'joined',
                    ],
                    'state_key' => '',
                    'type' => 'm.room.history_visibility',
                ],
            ],
            'name' => $name->toString(),
            'power_level_content_override' => [
                'ban' => $botPowerLevel->toInt(),
                'events' => [
                    // https://spec.matrix.org/latest/client-server-api/#mroomavatar
                    'm.room.avatar' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomcanonical_alias
                    'm.room.canonical_alias' => Matrix\Domain\PowerLevel::staff()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomencryption
                    'm.room.encryption' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomguest_access
                    'm.room.guest_access' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomhistory_visibility
                    'm.room.history_visibility' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomjoin_rules
                    'm.room.join_rules' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomname
                    'm.room.name' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroompower_levels
                    'm.room.power_levels' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomserver_acl
                    'm.room.server_acl' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomtombstone
                    'm.room.tombstone' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    // https://spec.matrix.org/latest/client-server-api/#mroomtopic
                    'm.room.topic' => Matrix\Domain\PowerLevel::bot()->toInt(),
                ],
                'events_default' => 0,
                'invite' => $botPowerLevel->toInt(),
                'kick' => $botPowerLevel->toInt(),
                'redact' => $redactorPowerLevel->toInt(),
                'state_default' => $staffPowerLevel->toInt(),
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

    public function updateRoom(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\RoomName $roomName,
        Matrix\Domain\RoomTopic $roomTopic
    ): void {
        $this->api->setState(
            $roomId,
            Matrix\Domain\EventType::fromString('m.room.name'),
            Matrix\Domain\StateKey::fromString(''),
            [
                'name' => $roomName->toString(),
            ],
        );

        $this->api->setState(
            $roomId,
            Matrix\Domain\EventType::fromString('m.room.topic'),
            Matrix\Domain\StateKey::fromString(''),
            [
                'topic' => $roomTopic->toString(),
            ],
        );
    }

    public function synchronizeRoomMembers(
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
