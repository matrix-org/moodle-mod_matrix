<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\test\unit\twitter;

use Ergebnis\Test\Util;
use mod_matrix\twitter\bootstrap;
use PHPUnit\Framework;

/**
 * @covers \mod_matrix\twitter\bootstrap
 *
 * @internal
 */
final class BootstrapTest extends Framework\TestCase
{
    use Util\Helper;

    public function testAlertRejectsUnknownType(): void
    {
        $faker = self::faker();

        $type = $faker->sentence();
        $content = $faker->sentence();

        $types = [
            'danger',
            'dark',
            'info',
            'light',
            'primary',
            'secondary',
            'success',
            'warning',
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Type needs to be one "%s", got "%s" instead.',
            implode('", "', $types),
            $type
        ));

        bootstrap::alert(
            $type,
            $content
        );
    }

    /**
     * @dataProvider provideKnownType
     */
    public function testAlertReturnsHtmlForKnownType(string $type): void
    {
        $faker = self::faker();

        $content = $faker->sentence();

        $alert = bootstrap::alert(
            $type,
            $content
        );

        $expexted = <<<TXT
<div class="alert alert-${type}">
    ${content}
</div>
TXT;

        self::assertSame($expexted, $alert);
    }

    public function provideKnownType(): \Generator
    {
        foreach (self::knownTypes() as $type) {
            yield $type => [
                $type,
            ];
        }
    }

    /**
     * @return array<int, string>
     */
    private static function knownTypes(): array
    {
        return [
            'danger',
            'dark',
            'info',
            'light',
            'primary',
            'secondary',
            'success',
            'warning',
        ];
    }
}
