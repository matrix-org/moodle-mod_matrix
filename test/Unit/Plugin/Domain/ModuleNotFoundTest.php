<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\ModuleNotFound
 *
 * @uses \mod_matrix\Plugin\Domain\ModuleId
 */
final class ModuleNotFoundTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForReturnsException(): void
    {
        $moduleId = Plugin\Domain\ModuleId::fromInt(self::faker()->numberBetween(1));

        $exception = Plugin\Domain\ModuleNotFound::for($moduleId);

        $expected = \sprintf(
            'Could not find module with id %d.',
            $moduleId->toInt(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
