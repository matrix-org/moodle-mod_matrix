<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\test\unit;

use mod_matrix\container;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\container
 */
final class ContainerTest extends Framework\TestCase
{
    public function testInstanceReturnsSameContainer(): void
    {
        $container = container::instance();

        self::assertSame($container, container::instance());
    }
}
