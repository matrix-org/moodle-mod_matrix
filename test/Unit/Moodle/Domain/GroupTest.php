<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\Group
 *
 * @uses \mod_matrix\Moodle\Domain\GroupId
 * @uses \mod_matrix\Moodle\Domain\Name
 */
final class GroupTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsGroup(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));
        $name = Moodle\Domain\Name::fromString($faker->sentence());

        $group = Moodle\Domain\Group::create(
            $id,
            $name,
        );

        self::assertSame($id, $group->id());
        self::assertSame($name, $group->name());
    }
}
