<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\ModuleNotFound
 *
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 */
final class ModuleNotFoundTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForReturnsException(): void
    {
        $moduleId = Moodle\Domain\ModuleId::fromInt(self::faker()->numberBetween(1));

        $exception = Moodle\Domain\ModuleNotFound::for($moduleId);

        $expected = \sprintf(
            'Could not find module with id %d.',
            $moduleId->toInt(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
