<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\User
 *
 * @uses \mod_matrix\Matrix\Domain\UserId
 * @uses \mod_matrix\Moodle\Domain\UserId
 */
final class UserTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsUser(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\UserId::fromInt($faker->numberBetween(1));
        $matrixUserId = Test\Util\Matrix\Domain\UserIdFactory::create($faker);

        $user = Moodle\Domain\User::create(
            $id,
            $matrixUserId,
        );

        self::assertSame($id, $user->id());
        self::assertSame($matrixUserId, $user->matrixUserId());
    }
}
