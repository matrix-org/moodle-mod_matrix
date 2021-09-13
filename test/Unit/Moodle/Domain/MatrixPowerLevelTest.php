<?php

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
 * @covers \mod_matrix\Moodle\Domain\MatrixPowerLevel
 */
final class MatrixPowerLevelTest extends Framework\TestCase
{
    public function testBotReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Moodle\Domain\MatrixPowerLevel::bot();

        self::assertSame(100, $matrixPowerLevel->toInt());
    }

    public function testStaffReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Moodle\Domain\MatrixPowerLevel::staff();

        self::assertSame(99, $matrixPowerLevel->toInt());
    }

    public function testRedactorReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Moodle\Domain\MatrixPowerLevel::redactor();

        self::assertSame(50, $matrixPowerLevel->toInt());
    }

    public function testDefaultReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Moodle\Domain\MatrixPowerLevel::default();

        self::assertSame(0, $matrixPowerLevel->toInt());
    }
}
