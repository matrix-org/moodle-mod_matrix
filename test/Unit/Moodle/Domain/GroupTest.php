<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
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
 * @uses \mod_matrix\Moodle\Domain\GroupName
 */
final class GroupTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsGroup(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));
        $name = Moodle\Domain\GroupName::fromString($faker->sentence());

        $group = Moodle\Domain\Group::create(
            $id,
            $name,
        );

        self::assertSame($id, $group->id());
        self::assertSame($name, $group->name());
    }
}
