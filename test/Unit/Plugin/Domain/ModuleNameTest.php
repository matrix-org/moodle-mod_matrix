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
 * @covers \mod_matrix\Plugin\Domain\ModuleName
 */
final class ModuleNameTest extends Framework\TestCase
{
    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Plugin\Domain\ModuleNameProvider::tooLong()
     */
    public function testFromStringRejectsValueWhenItIsTooLong(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value "%s" is longer than %d characters.',
            $value,
            Plugin\Domain\ModuleName::LENGTH_MAX,
        ));

        Plugin\Domain\ModuleName::fromString($value);
    }

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Plugin\Domain\ModuleNameProvider::notTooLong()
     */
    public function testFromStringReturnsNameWhenItIsNotTooLong(string $value): void
    {
        $name = Plugin\Domain\ModuleName::fromString($value);

        self::assertSame($value, $name->toString());
    }
}
