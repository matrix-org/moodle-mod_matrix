<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\CourseId
 */
final class CourseIdTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsCourseId(int $value): void
    {
        $courseId = Moodle\Domain\CourseId::fromInt($value);

        self::assertSame($value, $courseId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsCourseId(int $value): void
    {
        $courseId = Moodle\Domain\CourseId::fromString((string) $value);

        self::assertSame($value, $courseId->toInt());
    }
}
