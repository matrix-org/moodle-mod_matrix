<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use Ergebnis\Test\Util;
use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\ModuleId
 */
final class ModuleIdTest extends Framework\TestCase
{
    use Util\Helper;

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsModuleId(int $value): void
    {
        $moduleId = Moodle\Domain\ModuleId::fromInt($value);

        self::assertSame($value, $moduleId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsModuleId(int $value): void
    {
        $moduleId = Moodle\Domain\ModuleId::fromString((string) $value);

        self::assertSame($value, $moduleId->toInt());
    }

    public function testUnknownReturnsModuleId(): void
    {
        $moduleId = Moodle\Domain\ModuleId::unknown();

        self::assertSame(-1, $moduleId->toInt());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $two = Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenValueIsSame(): void
    {
        $value = self::faker()->numberBetween(1);

        $one = Moodle\Domain\ModuleId::fromInt($value);
        $two = Moodle\Domain\ModuleId::fromInt($value);

        self::assertTrue($one->equals($two));
    }
}
