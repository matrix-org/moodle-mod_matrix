<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\Type
 */
final class TypeTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsType(int $value): void
    {
        $type = Matrix\Domain\Type::fromString((string) $value);

        self::assertSame($value, $type->toInt());
    }
}
