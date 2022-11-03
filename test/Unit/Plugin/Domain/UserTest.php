<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Matrix;
use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\User
 *
 * @uses \mod_matrix\Matrix\Domain\Homeserver
 * @uses \mod_matrix\Matrix\Domain\UserId
 * @uses \mod_matrix\Matrix\Domain\Username
 * @uses \mod_matrix\Plugin\Domain\UserId
 */
final class UserTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsUser(): void
    {
        $faker = self::faker();

        $id = Plugin\Domain\UserId::fromInt($faker->numberBetween(1));
        $matrixUserId = Test\Util\Matrix\Domain\UserIdFactory::create($faker);

        $user = Plugin\Domain\User::create(
            $id,
            $matrixUserId,
        );

        self::assertSame($id, $user->id());
        self::assertSame($matrixUserId, $user->matrixUserId());
    }
}
