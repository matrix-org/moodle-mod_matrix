<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\CourseId
 */
final class CourseIdTest extends Framework\TestCase
{
    use Util\Helper;

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsCourseId(int $value): void
    {
        $courseId = Matrix\Domain\CourseId::fromString((string) $value);

        self::assertSame($value, $courseId->toInt());
    }
}
