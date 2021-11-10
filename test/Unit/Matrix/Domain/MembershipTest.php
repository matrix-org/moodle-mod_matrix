<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
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
