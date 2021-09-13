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
 * @covers \mod_matrix\Matrix\Domain\MatrixUserId
 */
final class MatrixUserIdTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::arbitrary()
     */
    public function testFromStringReturnsMatrixUserId(string $value): void
    {
        $matrixUserId = Matrix\Domain\MatrixUserId::fromString($value);

        self::assertSame($value, $matrixUserId->toString());
    }
}
