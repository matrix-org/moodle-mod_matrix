<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\UserIdCollection
 *
 * @uses \mod_matrix\Matrix\Domain\Homeserver
 * @uses \mod_matrix\Matrix\Domain\UserId
 * @uses \mod_matrix\Matrix\Domain\Username
 */
final class UserIdCollectionTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromUsersIdsReturnsUserIdCollection(): void
    {
        $faker = self::faker();

        $userIds = [
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
        ];

        $collection = Matrix\Domain\UserIdCollection::fromUserIds(...$userIds);

        self::assertSame($userIds, $collection->toArray());
    }

    public function testDiffReturnsUserIdCollection(): void
    {
        $faker = self::faker();

        $userIdOne = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdTwo = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdThree = Test\Util\Matrix\Domain\UserIdFactory::create($faker);

        $userIdsOne = [
            $userIdOne,
            $userIdTwo,
            $userIdThree,
        ];

        $userIdFour = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdFive = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdSix = Test\Util\Matrix\Domain\UserIdFactory::create($faker);

        $userIdsTwo = [
            $userIdTwo,
            $userIdFour,
            $userIdFive,
            $userIdSix,
        ];

        $one = Matrix\Domain\UserIdCollection::fromUserIds(...$userIdsOne);
        $two = Matrix\Domain\UserIdCollection::fromUserIds(...$userIdsTwo);

        $diff = $one->diff($two);

        $expected = [
            $userIdOne,
            $userIdThree,
        ];

        self::assertSame($expected, $diff->toArray());

        self::assertNotSame($one, $diff);
        self::assertNotSame($two, $diff);

        self::assertSame($userIdsOne, $one->toArray());
        self::assertSame($userIdsTwo, $two->toArray());
    }

    public function testFilterReturnsUserIdCollection(): void
    {
        $faker = self::faker();

        $userIdOne = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdTwo = Test\Util\Matrix\Domain\UserIdFactory::create($faker);
        $userIdThree = Test\Util\Matrix\Domain\UserIdFactory::create($faker);

        $userIds = [
            $userIdOne,
            $userIdTwo,
            $userIdThree,
        ];

        $collection = Matrix\Domain\UserIdCollection::fromUserIds(...$userIds);

        $filtered = $collection->filter(static function (Matrix\Domain\UserId $userId) use ($userIdTwo): bool {
            return !$userId->equals($userIdTwo);
        });

        $expected = [
            $userIdOne,
            $userIdThree,
        ];

        self::assertSame($expected, $filtered->toArray());

        self::assertNotSame($collection, $filtered);

        self::assertSame($userIds, $collection->toArray());
    }

    public function testMergeReturnsUserIdCollection(): void
    {
        $faker = self::faker();

        $userIdsOne = [
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
        ];

        $userIdsTwo = [
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
            Test\Util\Matrix\Domain\UserIdFactory::create($faker),
        ];

        $one = Matrix\Domain\UserIdCollection::fromUserIds(...$userIdsOne);
        $two = Matrix\Domain\UserIdCollection::fromUserIds(...$userIdsTwo);

        $merged = $one->merge($two);

        $expected = \array_merge(
            $userIdsOne,
            $userIdsTwo,
        );

        self::assertSame($expected, $merged->toArray());

        self::assertNotSame($one, $merged);
        self::assertNotSame($two, $merged);

        self::assertSame($userIdsOne, $one->toArray());
        self::assertSame($userIdsTwo, $two->toArray());
    }
}
