<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\Membership
 */
final class MembershipTest extends Framework\TestCase
{
    public function testBanReturnsMembership(): void
    {
        $membership = Matrix\Domain\Membership::ban();

        self::assertSame('ban', $membership->toString());
    }

    public function testInviteReturnsMembership(): void
    {
        $membership = Matrix\Domain\Membership::invite();

        self::assertSame('invite', $membership->toString());
    }

    public function testJoinReturnsMembership(): void
    {
        $membership = Matrix\Domain\Membership::join();

        self::assertSame('join', $membership->toString());
    }

    public function testLeaveReturnsMembership(): void
    {
        $membership = Matrix\Domain\Membership::leave();

        self::assertSame('leave', $membership->toString());
    }
}
