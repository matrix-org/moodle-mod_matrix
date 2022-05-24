<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\PowerLevel
 */
final class PowerLevelTest extends Framework\TestCase
{
    public function testBotReturnsPowerLevel(): void
    {
        $powerLevel = Matrix\Domain\PowerLevel::bot();

        self::assertSame(100, $powerLevel->toInt());
    }

    public function testStaffReturnsPowerLevel(): void
    {
        $powerLevel = Matrix\Domain\PowerLevel::staff();

        self::assertSame(99, $powerLevel->toInt());
    }

    public function testRedactorReturnsPowerLevel(): void
    {
        $powerLevel = Matrix\Domain\PowerLevel::redactor();

        self::assertSame(50, $powerLevel->toInt());
    }

    public function testDefaultReturnsPowerLevel(): void
    {
        $powerLevel = Matrix\Domain\PowerLevel::default();

        self::assertSame(0, $powerLevel->toInt());
    }
}
