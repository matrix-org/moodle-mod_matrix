<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\Username
 */
final class UsernameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Matrix\Domain\UsernameProvider::invalid()
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value "%s" does not appear to be a valid Matrix username.',
            $value,
        ));

        Matrix\Domain\Username::fromString($value);
    }

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Matrix\Domain\UsernameProvider::valid()
     */
    public function testFromStringReturnsUsername(string $value): void
    {
        $username = Matrix\Domain\Username::fromString($value);

        self::assertSame($value, $username->toString());
    }
}
