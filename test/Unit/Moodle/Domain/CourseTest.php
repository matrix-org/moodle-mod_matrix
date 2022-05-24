<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\Course
 *
 * @uses \mod_matrix\Moodle\Domain\CourseFullName
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\CourseShortName
 */
final class CourseTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsCourse(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\CourseId::fromInt($faker->numberBetween(1));
        $fullName = Moodle\Domain\CourseFullName::fromString($faker->sentence());
        $shortName = Moodle\Domain\CourseShortName::fromString($faker->sentence());

        $course = Moodle\Domain\Course::create(
            $id,
            $fullName,
            $shortName,
        );

        self::assertSame($id, $course->id());
        self::assertSame($fullName, $course->fullName());
        self::assertSame($shortName, $course->shortName());
    }
}
