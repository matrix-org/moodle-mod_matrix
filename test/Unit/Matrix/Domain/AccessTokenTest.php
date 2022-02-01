<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\AccessToken
 */
final class AccessTokenTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function testFromStringReturnsAccessToken(string $value): void
    {
        $accessToken = Matrix\Domain\AccessToken::fromString($value);

        self::assertSame($value, $accessToken->toString());
    }
}
