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
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\ModuleTarget
 */
final class ModuleTargetTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary
     */
    public function testFromStringRejectsUnknownValue(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to be one of "%s", got "%s" instead.',
            \implode('", "', [
                Moodle\Domain\ModuleTarget::elementUrl()->toString(),
                Moodle\Domain\ModuleTarget::matrixTo()->toString(),
            ]),
            $value,
        ));

        Moodle\Domain\ModuleTarget::fromString($value);
    }

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Moodle\Domain\TargetProvider::known()
     */
    public function testFromStringReturnsModuleTarget(string $value): void
    {
        $target = Moodle\Domain\ModuleTarget::fromString($value);

        self::assertSame($value, $target->toString());
    }

    public function testElementUrlReturnsModuleTarget(): void
    {
        $target = Moodle\Domain\ModuleTarget::elementUrl();

        self::assertSame('element-url', $target->toString());
    }

    public function testMatrixToReturnsTarget(): void
    {
        $target = Moodle\Domain\ModuleTarget::matrixTo();

        self::assertSame('matrix-to', $target->toString());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $one = Moodle\Domain\ModuleTarget::matrixTo();
        $two = Moodle\Domain\ModuleTarget::elementUrl();

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $one = Moodle\Domain\ModuleTarget::elementUrl();
        $two = Moodle\Domain\ModuleTarget::elementUrl();

        self::assertTrue($one->equals($two));
    }
}
