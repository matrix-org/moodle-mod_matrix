<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Matrix;
use mod_matrix\Plugin;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\ModuleTarget
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
                Plugin\Domain\ModuleTarget::elementUrl()->toString(),
                Plugin\Domain\ModuleTarget::matrixTo()->toString(),
            ]),
            $value,
        ));

        Plugin\Domain\ModuleTarget::fromString($value);
    }

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Plugin\Domain\ModuleTargetProvider::known()
     */
    public function testFromStringReturnsModuleTarget(string $value): void
    {
        $target = Plugin\Domain\ModuleTarget::fromString($value);

        self::assertSame($value, $target->toString());
    }

    public function testElementUrlReturnsModuleTarget(): void
    {
        $target = Plugin\Domain\ModuleTarget::elementUrl();

        self::assertSame('element-url', $target->toString());
    }

    public function testMatrixToReturnsTarget(): void
    {
        $target = Plugin\Domain\ModuleTarget::matrixTo();

        self::assertSame('matrix-to', $target->toString());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $one = Plugin\Domain\ModuleTarget::matrixTo();
        $two = Plugin\Domain\ModuleTarget::elementUrl();

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $one = Plugin\Domain\ModuleTarget::elementUrl();
        $two = Plugin\Domain\ModuleTarget::elementUrl();

        self::assertTrue($one->equals($two));
    }
}
