<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\ModuleName
 */
final class ModuleNameTest extends Framework\TestCase
{
    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Moodle\Domain\ModuleNameProvider::tooLong()
     */
    public function testFromStringRejectsValueWhenItIsTooLong(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value "%s" is longer than %d characters.',
            $value,
            Moodle\Domain\ModuleName::LENGTH_MAX,
        ));

        Moodle\Domain\ModuleName::fromString($value);
    }

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Moodle\Domain\ModuleNameProvider::notTooLong()
     */
    public function testFromStringReturnsNameWhenItIsNotTooLong(string $value): void
    {
        $name = Moodle\Domain\ModuleName::fromString($value);

        self::assertSame($value, $name->toString());
    }
}
