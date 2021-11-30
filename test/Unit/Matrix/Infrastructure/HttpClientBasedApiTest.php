<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Infrastructure\HttpClientBasedApi
 *
 * @uses \mod_matrix\Matrix\Domain\EventType
 * @uses \mod_matrix\Matrix\Domain\Membership
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Matrix\Domain\StateKey
 * @uses \mod_matrix\Matrix\Domain\UserId
 */
final class HttpClientBasedApiTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testWhoAmIReturnsUserId(): void
    {
        $faker = self::faker();

        $userId = \sprintf(
            '@%s:%s',
            $faker->userName(),
            $faker->domainName(),
        );

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('get')
            ->with(self::identicalTo('/_matrix/client/r0/account/whoami'))
            ->willReturn([
                'user_id' => $userId,
            ]);

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $actual = $api->whoAmI();

        self::assertEquals(Matrix\Domain\UserId::fromString($userId), $actual);
    }

    public function testCreateRoomReturnsRoomId(): void
    {
        $faker = self::faker();

        $options = [
            'creation_content' => [
                'm.federate' => $faker->boolean(),
            ],
            'name' => $faker->sentence(),
            'preset' => $faker->randomElement([
                'private_chat',
                'public_chat',
                'trusted_private_chat',
            ]),
            'room_alias_name' => $faker->word(),
            'topic' => $faker->sentence(),
        ];

        $roomId = $faker->sha1();

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('post')
            ->with(
                self::identicalTo('/_matrix/client/r0/createRoom'),
                self::identicalTo($options),
            )
            ->willReturn([
                'room_id' => $roomId,
            ]);

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $actual = $api->createRoom($options);

        self::assertEquals(Matrix\Domain\RoomId::fromString($roomId), $actual);
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function testInviteUserInvitesUser(string $value): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($value);

        $userId = Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->userName(),
            $faker->domainName(),
        ));

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('post')
            ->with(
                self::identicalTo(\sprintf(
                    '/_matrix/client/r0/rooms/%s/invite',
                    \urlencode($roomId->toString()),
                )),
                self::identicalTo([
                    'user_id' => $userId->toString(),
                ]),
            )
            ->willReturn([
                'room_id' => \urlencode($roomId->toString()),
            ]);

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $api->inviteUser(
            $roomId,
            $userId,
        );
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function testKickUserKicksUser(string $value): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($value);

        $userId = Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->userName(),
            $faker->domainName(),
        ));

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('post')
            ->with(
                self::identicalTo(\sprintf(
                    '/_matrix/client/r0/rooms/%s/kick',
                    \urlencode($roomId->toString()),
                )),
                self::identicalTo([
                    'user_id' => $userId->toString(),
                ]),
            )
            ->willReturn([
                'room_id' => \urlencode($roomId->toString()),
            ]);

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $api->kickUser(
            $roomId,
            $userId,
        );
    }

    public function testGetStateReturnsState(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $eventType = Matrix\Domain\EventType::fromString($faker->word());
        $stateKey = Matrix\Domain\StateKey::fromString($faker->word());

        $state = \array_combine(
            $faker->words(),
            $faker->words(),
        );

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('get')
            ->with(self::identicalTo(\sprintf(
                '/_matrix/client/r0/rooms/%s/state/%s/%s',
                \urlencode($roomId->toString()),
                \urlencode($eventType->toString()),
                \urlencode($stateKey->toString()),
            )))
            ->willReturn($state);

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $actual = $api->getState(
            $roomId,
            $eventType,
            $stateKey,
        );

        self::assertSame($state, $actual);
    }

    public function testSetStateSetsStateWhenStateKeyIsEmptyString(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $eventType = Matrix\Domain\EventType::fromString($faker->word());
        $stateKey = Matrix\Domain\StateKey::fromString('');
        $state = \array_combine(
            $faker->words(),
            $faker->words(),
        );

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('put')
            ->with(
                self::identicalTo(\sprintf(
                    '/_matrix/client/r0/rooms/%s/state/%s',
                    \urlencode($roomId->toString()),
                    \urlencode($eventType->toString()),
                )),
                self::identicalTo($state),
            );

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $api->setState(
            $roomId,
            $eventType,
            $stateKey,
            $state,
        );
    }

    public function testSetStateSetsStateWhenStateKeyIsNotAnEmptyString(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $eventType = Matrix\Domain\EventType::fromString($faker->word());
        $stateKey = Matrix\Domain\StateKey::fromString($faker->word());
        $state = \array_combine(
            $faker->words(),
            $faker->words(),
        );

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('put')
            ->with(
                self::identicalTo(\sprintf(
                    '/_matrix/client/r0/rooms/%s/state/%s/%s',
                    \urlencode($roomId->toString()),
                    \urlencode($eventType->toString()),
                    \urlencode($stateKey->toString()),
                )),
                self::identicalTo($state),
            );

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $api->setState(
            $roomId,
            $eventType,
            $stateKey,
            $state,
        );
    }

    public function testListUsersReturnsEmptyUserIdCollectionWhenRoomDoesNotHaveAnyMembers(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('get')
            ->with(self::identicalTo(\sprintf(
                '/_matrix/client/r0/rooms/%s/members',
                \urlencode($roomId->toString()),
            )))
            ->willReturn([
                'chunk' => [],
            ]);

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $actual = $api->listUsers($roomId);

        self::assertEquals(Matrix\Domain\UserIdCollection::fromUserIds(), $actual);
    }

    public function testListUsersReturnsUserIdColletionWithUserIdsWhenRoomHasInvitedOrJoinedMembers(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());

        $userIdOne = \sprintf(
            '@%s:%s',
            $faker->userName(),
            $faker->domainName(),
        );

        $userIdTwo = \sprintf(
            '@%s:%s',
            $faker->userName(),
            $faker->domainName(),
        );

        $userIdThree = \sprintf(
            '@%s:%s',
            $faker->userName(),
            $faker->domainName(),
        );

        $userIdFour = \sprintf(
            '@%s:%s',
            $faker->userName(),
            $faker->domainName(),
        );

        $httpClient = $this->createMock(Matrix\Infrastructure\HttpClient::class);

        $httpClient
            ->expects(self::once())
            ->method('get')
            ->with(self::identicalTo(\sprintf(
                '/_matrix/client/r0/rooms/%s/members',
                \urlencode($roomId->toString()),
            )))
            ->willReturn([
                'chunk' => [
                    $faker->word(),
                    [
                        'foo' => null,
                    ],
                    [
                        'content' => null,
                    ],
                    [
                        'content' => [],
                    ],
                    [
                        'content' => $faker->words(),
                    ],
                    [
                        'content' => [
                            'membership' => $faker->words(),
                        ],
                    ],
                    [
                        'content' => [
                            'membership' => 'ban',
                        ],
                        'state_key' => $userIdOne,
                    ],
                    [
                        'content' => [
                            'membership' => 'invite',
                        ],
                        'state_key' => $userIdTwo,
                    ],
                    [
                        'content' => [
                            'membership' => 'join',
                        ],
                        'state_key' => $userIdThree,
                    ],
                    [
                        'content' => [
                            'membership' => 'leave',
                        ],
                        'state_key' => $userIdFour,
                    ],
                ],
            ]);

        $api = new Matrix\Infrastructure\HttpClientBasedApi($httpClient);

        $actual = $api->listUsers($roomId);

        $expected = Matrix\Domain\UserIdCollection::fromUserIds(
            Matrix\Domain\UserId::fromString($userIdTwo),
            Matrix\Domain\UserId::fromString($userIdThree),
        );

        self::assertEquals($expected, $actual);
    }
}
