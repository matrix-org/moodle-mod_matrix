<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\ModuleId
 */
final class ModuleIdTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsModuleId(int $value): void
    {
        $moduleId = Plugin\Domain\ModuleId::fromInt($value);

        self::assertSame($value, $moduleId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsModuleId(int $value): void
    {
        $moduleId = Plugin\Domain\ModuleId::fromString((string) $value);

        self::assertSame($value, $moduleId->toInt());
    }

    public function testUnknownReturnsModuleId(): void
    {
        $moduleId = Plugin\Domain\ModuleId::unknown();

        self::assertSame(-1, $moduleId->toInt());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $two = Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = self::faker()->numberBetween(1);

        $one = Plugin\Domain\ModuleId::fromInt($value);
        $two = Plugin\Domain\ModuleId::fromInt($value);

        self::assertTrue($one->equals($two));
    }
}
