<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Application;

use Ergebnis\Clock;
use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Application\MatrixService
 *
 * @uses \mod_matrix\Matrix\Application\Configuration
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\CourseId
 */
final class MatrixServiceTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function testUrlForRoomReturnsUrlForOpeningRoomInBrowserWhenElementUrlIsBlankOrEmpty(string $elementUrl): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());

        $configuration = Matrix\Application\Configuration::fromObject((object) [
            'access_token' => $faker->sha1(),
            'element_url' => $elementUrl,
            'homeserver_url' => \sprintf(
                'https://%s',
                $faker->domainName(),
            ),
        ]);

        $matrixService = new Matrix\Application\MatrixService(
            $this->createStub(Matrix\Application\Api::class),
            $configuration,
            $this->createStub(Moodle\Domain\ModuleRepository::class),
            $this->createStub(Moodle\Domain\RoomRepository::class),
            $this->createStub(Clock\Clock::class),
        );

        $url = $matrixService->urlForRoom($roomId);

        $expected = \sprintf(
            'https://matrix.to/#/%s',
            $roomId->toString(),
        );

        self::assertSame($expected, $url);
    }

    public function testUrlForRoomReturnsUrlForOpeningRoomInBrowserWhenElementUrlIsNotBlankOrEmpty(): void
    {
        $faker = self::faker();

        $roomId = Matrix\Domain\RoomId::fromString($faker->sha1());

        $elementUrl = \sprintf(
            'https://%s',
            $faker->domainName(),
        );

        $configuration = Matrix\Application\Configuration::fromObject((object) [
            'access_token' => $faker->sha1(),
            'element_url' => $elementUrl,
            'homeserver_url' => \sprintf(
                'https://%s',
                $faker->domainName(),
            ),
        ]);

        $matrixService = new Matrix\Application\MatrixService(
            $this->createStub(Matrix\Application\Api::class),
            $configuration,
            $this->createStub(Moodle\Domain\ModuleRepository::class),
            $this->createStub(Moodle\Domain\RoomRepository::class),
            $this->createStub(Clock\Clock::class),
        );

        $url = $matrixService->urlForRoom($roomId);

        $expected = \sprintf(
            '%s/#/room/%s',
            $elementUrl,
            $roomId->toString(),
        );

        self::assertSame($expected, $url);
    }
}
