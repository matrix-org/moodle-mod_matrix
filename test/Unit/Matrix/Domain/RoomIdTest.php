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
 * @covers \mod_matrix\Matrix\Domain\RoomId
 */
final class RoomIdTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function testFromStringReturnsRoomId(string $value): void
    {
        $roomId = Matrix\Domain\RoomId::fromString($value);

        self::assertSame($value, $roomId->toString());
    }
}
