<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_plugin\Test\Unit\Plugin\Domain;

use mod_matrix\Plugin;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\Url
 */
final class UrlTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function testFromStringReturnsUrl(string $value): void
    {
        $url = Plugin\Domain\Url::fromString($value);

        self::assertSame($value, $url->toString());
    }
}
