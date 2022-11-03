<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit;

use mod_matrix\Container;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Container
 */
final class ContainerTest extends Framework\TestCase
{
    public function testInstanceReturnsSameContainer(): void
    {
        $container = Container::instance();

        self::assertSame($container, Container::instance());
    }
}
