<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Plugin;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\ModuleType
 */
final class ModuleTypeTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsType(int $value): void
    {
        $type = Plugin\Domain\ModuleType::fromInt($value);

        self::assertSame($value, $type->toInt());
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsType(int $value): void
    {
        $type = Plugin\Domain\ModuleType::fromString((string) $value);

        self::assertSame($value, $type->toInt());
    }
}
