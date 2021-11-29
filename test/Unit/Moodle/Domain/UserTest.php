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
 */
final class UserTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsUserWhenMatrixUserIdIsNull(): void
    {
        $user = Moodle\Domain\User::create(null);

        self::assertNull($user->matrixUserId());
    }

    public function testCreateReturnsUserWhenMatrixUserIdIsNotNull(): void
    {
        $matrixUserId = Matrix\Domain\UserId::fromString(self::faker()->sha1());

        $user = Moodle\Domain\User::create($matrixUserId);

        self::assertSame($matrixUserId, $user->matrixUserId());
    }
}
