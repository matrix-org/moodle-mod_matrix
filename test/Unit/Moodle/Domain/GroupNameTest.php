<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\GroupName
 */
final class GroupNameTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function testFromStringReturnsGroupName(string $value): void
    {
        $name = Moodle\Domain\GroupName::fromString($value);

        self::assertSame($value, $name->toString());
    }
}
