<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\UserId
 */
final class UserIdTest extends Framework\TestCase
{
    use Util\Helper;

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::arbitrary()
     */
    public function testFromStringReturnsMatrixUserId(string $value): void
    {
        $userId = Matrix\Domain\UserId::fromString($value);

        self::assertSame($value, $userId->toString());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Matrix\Domain\UserId::fromString($faker->sha1());
        $two = Matrix\Domain\UserId::fromString($faker->sha1());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = self::faker()->sha1();

        $one = Matrix\Domain\UserId::fromString($value);
        $two = Matrix\Domain\UserId::fromString($value);

        self::assertTrue($one->equals($two));
    }
}
