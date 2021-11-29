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
 * @uses \mod_matrix\Matrix\Domain\UserId
 */
final class UserIdCollectionTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromUsersIdsReturnsUserIdCollection(): void
    {
        $faker = self::faker();

        $userIds = [
            Matrix\Domain\UserId::fromString($faker->sha1()),
            Matrix\Domain\UserId::fromString($faker->sha1()),
            Matrix\Domain\UserId::fromString($faker->sha1()),
        ];

        $collection = Matrix\Domain\UserIdCollection::fromUserIds(...$userIds);

        self::assertSame($userIds, $collection->toArray());
    }
}
