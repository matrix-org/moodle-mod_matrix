<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Matrix;
use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\RoomLink
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

        $roomLink = Plugin\Domain\RoomLink::create(
            $url,
            $roomName,
        );

        self::assertSame($url, $roomLink->url());
        self::assertSame($roomName, $roomLink->roomName());
    }
}
