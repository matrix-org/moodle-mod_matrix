<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Application;

use mod_matrix\Plugin;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Application\Plugin
 */
final class PluginTest extends Framework\TestCase
{
    public function testConstants(): void
    {
        self::assertSame('matrix', Plugin\Application\Plugin::NAME);
    }
}
