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
 * @covers \mod_matrix\Moodle\Domain\GroupId
 */
final class GroupIdTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsGroupId(int $value): void
    {
        $groupId = Moodle\Domain\GroupId::fromInt($value);

        self::assertSame($value, $groupId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsGroupId(int $value): void
    {
        $groupId = Moodle\Domain\GroupId::fromString((string) $value);

        self::assertSame($value, $groupId->toInt());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));
        $two = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = self::faker()->numberBetween(1);

        $one = Moodle\Domain\GroupId::fromInt($value);
        $two = Moodle\Domain\GroupId::fromInt($value);

        self::assertTrue($one->equals($two));
    }
}
