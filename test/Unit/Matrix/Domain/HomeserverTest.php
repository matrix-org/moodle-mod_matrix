<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\Homeserver
 */
final class HomeserverTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Matrix\Domain\HomeserverProvider::invalid()
     */
    public function testFromStringRejectsBlankOrEmptyValue(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value "%s" does not appear to be a valid Matrix homeserver.',
            $value,
        ));

        Matrix\Domain\Homeserver::fromString($value);
    }

    /**
     * @dataProvider \mod_matrix\Test\DataProvider\Matrix\Domain\HomeserverProvider::valid()
     */
    public function testFromStringReturnsHomeserver(string $value): void
    {
        $homeserver = Matrix\Domain\Homeserver::fromString($value);

        self::assertSame($value, $homeserver->toString());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Matrix\Domain\Homeserver::fromString($faker->domainName());
        $two = Matrix\Domain\Homeserver::fromString($faker->domainName());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = self::faker()->domainName();

        $one = Matrix\Domain\Homeserver::fromString($value);
        $two = Matrix\Domain\Homeserver::fromString($value);

        self::assertTrue($one->equals($two));
    }
}
