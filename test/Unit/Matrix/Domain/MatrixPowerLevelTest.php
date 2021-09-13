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
 * @covers \mod_matrix\Matrix\Domain\MatrixPowerLevel
 */
final class MatrixPowerLevelTest extends Framework\TestCase
{
    public function testBotReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Matrix\Domain\MatrixPowerLevel::bot();

        self::assertSame(100, $matrixPowerLevel->toInt());
    }

    public function testStaffReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Matrix\Domain\MatrixPowerLevel::staff();

        self::assertSame(99, $matrixPowerLevel->toInt());
    }

    public function testRedactorReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Matrix\Domain\MatrixPowerLevel::redactor();

        self::assertSame(50, $matrixPowerLevel->toInt());
    }

    public function testDefaultReturnsMatrixPowerLevel(): void
    {
        $matrixPowerLevel = Matrix\Domain\MatrixPowerLevel::default();

        self::assertSame(0, $matrixPowerLevel->toInt());
    }
}
