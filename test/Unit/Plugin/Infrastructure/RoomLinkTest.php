<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Plugin\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Infrastructure\RoomLink
 *
 * @uses \mod_matrix\Matrix\Domain\RoomName
 * @uses \mod_matrix\Plugin\Domain\Url
 */
final class RoomLinkTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsRoomLink(): void
    {
        $faker = self::faker();

        $url = Plugin\Domain\Url::fromString($faker->url());
        $roomName = Matrix\Domain\RoomName::fromString($faker->sentence());

        $roomLink = Plugin\Infrastructure\RoomLink::create(
            $url,
            $roomName,
        );

        self::assertSame($url, $roomLink->url());
        self::assertSame($roomName, $roomLink->roomName());
    }
}
