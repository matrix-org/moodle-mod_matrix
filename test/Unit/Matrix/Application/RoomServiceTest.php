<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Matrix\Application;

use mod_matrix\Matrix;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Application\RoomService
 *
 * @uses \mod_matrix\Matrix\Domain\EventType
 * @uses \mod_matrix\Matrix\Domain\Homeserver
 * @uses \mod_matrix\Matrix\Domain\PowerLevel
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Matrix\Domain\RoomName
 * @uses \mod_matrix\Matrix\Domain\RoomTopic
 * @uses \mod_matrix\Matrix\Domain\StateKey
 * @uses \mod_matrix\Matrix\Domain\UserId
 * @uses \mod_matrix\Matrix\Domain\UserIdCollection
 * @uses \mod_matrix\Matrix\Domain\Username
 * @uses \mod_matrix\Plugin\Application\Configuration
 */
final class RoomServiceTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testRemoveRoomRemovesUsersInRoomWhenRoomHasOnlyBotUser(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());

        $userIdOfBot = Test\Util\Matrix\Domain\UserIdFactory::create($faker);

        $api = $this->createMock(Matrix\Application\Api::class);

        $api
            ->expects(self::once())
            ->method('listUsers')
            ->willReturn(Matrix\Domain\UserIdCollection::fromUserIds());

        $api
            ->expects(self::once())
            ->method('whoAmI')
            ->willReturn($userIdOfBot);

        $api
            ->expects(self::once())
            ->method('kickUser')
            ->with(
                self::identicalTo($roomId),
                self::identicalTo($userIdOfBot),
            );

        $roomService = new Matrix\Application\RoomService($api);

        $roomService->removeRoom($roomId);
    }

    public function testCreateRoomCreatesRoom(): void
    {
        $faker = self::faker();

        $name = Matrix\Domain\RoomName::fromString($faker->word());
        $topic = Matrix\Domain\RoomTopic::fromString($faker->sentence());
        $creationContent = [
            'foo' => $faker->words(),
            'bar' => $faker->sentence(),
        ];

        $userIdOfBot = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());

        $api = $this->createMock(Matrix\Application\Api::class);

        $api
            ->expects(self::once())
            ->method('whoAmI')
            ->willReturn($userIdOfBot);

        $api
            ->expects(self::once())
            ->method('createRoom')
            ->with(self::equalTo([
                'creation_content' => $creationContent,
                'initial_state' => [
                    [
                        'content' => [
                            'algorithm' => 'm.megolm.v1.aes-sha2',
                        ],
                        'state_key' => '',
                        'type' => 'm.room.encryption',
                    ],
                    [
                        'content' => [
                            'guest_access' => 'forbidden',
                        ],
                        'state_key' => '',
                        'type' => 'm.room.guest_access',
                    ],
                    [
                        'content' => [
                            'history_visibility' => 'shared',
                        ],
                        'state_key' => '',
                        'type' => 'm.room.history_visibility',
                    ],
                ],
                'name' => $name->toString(),
                'power_level_content_override' => [
                    'ban' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    'invite' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    'events' => [
                        'm.room.avatar' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.canonical_alias' => Matrix\Domain\PowerLevel::staff()->toInt(),
                        'm.room.encryption' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.guest_access' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.history_visibility' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.join_rules' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.name' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.power_levels' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.server_acl' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.tombstone' => Matrix\Domain\PowerLevel::bot()->toInt(),
                        'm.room.topic' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    ],
                    'events_default' => 0,
                    'kick' => Matrix\Domain\PowerLevel::bot()->toInt(),
                    'redact' => Matrix\Domain\PowerLevel::redactor()->toInt(),
                    'state_default' => Matrix\Domain\PowerLevel::staff()->toInt(),
                    'users' => [
                        $userIdOfBot->toString() => Matrix\Domain\PowerLevel::bot()->toInt(),
                    ],
                ],
                'preset' => 'private_chat',
                'topic' => $topic->toString(),
            ]))
            ->willReturn($roomId);

        $roomService = new Matrix\Application\RoomService($api);

        $actualRoomId = $roomService->createRoom(
            $name,
            $topic,
            $creationContent,
        );

        self::assertSame($roomId, $actualRoomId);
    }

    public function testRemoveRoomRemovesUsersInRoomWhenRoomHasBotUserAndOtherUsers(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());

        $userIdOfBot = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdOne = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdTwo = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdThree = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdFour = Test\Util\Matrix\Domain\UserIdFactory::create($faker);

        $userIds = [
            $userIdOne,
            $userIdTwo,
            $userIdOfBot,
            $userIdThree,
            $userIdFour,
        ];

        $api = $this->createMock(Matrix\Application\Api::class);

        $api
            ->expects(self::once())
            ->method('listUsers')
            ->willReturn(Matrix\Domain\UserIdCollection::fromUserIds(...$userIds));

        $api
            ->expects(self::once())
            ->method('whoAmI')
            ->willReturn($userIdOfBot);

        $api
            ->expects(self::exactly(\count($userIds)))
            ->method('kickUser')
            ->withConsecutive(
                [
                    self::identicalTo($roomId),
                    self::identicalTo($userIdOne),
                ],
                [
                    self::identicalTo($roomId),
                    self::identicalTo($userIdTwo),
                ],
                [
                    self::identicalTo($roomId),
                    self::identicalTo($userIdThree),
                ],
                [
                    self::identicalTo($roomId),
                    self::identicalTo($userIdFour),
                ],
                [
                    self::identicalTo($roomId),
                    self::identicalTo($userIdOfBot),
                ],
            );

        $roomService = new Matrix\Application\RoomService($api);

        $roomService->removeRoom($roomId);
    }

    public function testUpdateRoomUpdatesRoom(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $roomName = Matrix\Domain\RoomName::fromString($faker->sentence());
        $roomTopic = Matrix\Domain\RoomTopic::fromString($faker->sentence());

        $api = $this->createMock(Matrix\Application\Api::class);

        $api
            ->expects(self::exactly(2))
            ->method('setState')
            ->withConsecutive(
                [
                    self::identicalTo($roomId),
                    self::equalTo(Matrix\Domain\EventType::fromString('m.room.name')),
                    self::equalTo(Matrix\Domain\StateKey::fromString('')),
                    self::identicalTo([
                        'name' => $roomName->toString(),
                    ]),
                ],
                [
                    self::identicalTo($roomId),
                    self::equalTo(Matrix\Domain\EventType::fromString('m.room.topic')),
                    self::equalTo(Matrix\Domain\StateKey::fromString('')),
                    self::identicalTo([
                        'topic' => $roomTopic->toString(),
                    ]),
                ],
            );

        $roomService = new Matrix\Application\RoomService($api);

        $roomService->updateRoom(
            $roomId,
            $roomName,
            $roomTopic,
        );
    }
}
