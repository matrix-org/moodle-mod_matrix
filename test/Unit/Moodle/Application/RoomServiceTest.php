<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Application;

use Ergebnis\Clock;
use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Application\RoomService
 *
 * @uses \mod_matrix\Matrix\Application\RoomService
 * @uses \mod_matrix\Matrix\Domain\AccessToken
 * @uses \mod_matrix\Matrix\Domain\Homeserver
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Matrix\Domain\Url
 * @uses \mod_matrix\Matrix\Domain\UserId
 * @uses \mod_matrix\Matrix\Domain\Username
 * @uses \mod_matrix\Moodle\Application\Configuration
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\Module
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\ModuleName
 * @uses \mod_matrix\Moodle\Domain\ModuleNotFound
 * @uses \mod_matrix\Moodle\Domain\ModuleTarget
 * @uses \mod_matrix\Moodle\Domain\ModuleTopic
 * @uses \mod_matrix\Moodle\Domain\ModuleType
 * @uses \mod_matrix\Moodle\Domain\Room
 * @uses \mod_matrix\Moodle\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 */
final class RoomServiceTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function testUrlForRoomReturnsUrlForOpeningRoomViaMatrixToWhenElementUrlIsBlankOrEmpty(string $elementUrl): void
    {
        $faker = self::faker();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            null,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $matrixUserId = Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->word(),
            $faker->domainName(),
        ));

        $configuration = Moodle\Application\Configuration::fromObject((object) [
            'access_token' => $faker->sha1(),
            'element_url' => $elementUrl,
            'homeserver_url' => \sprintf(
                'https://%s.%s',
                $faker->word(),
                $faker->domainName(),
            ),
        ]);

        $roomService = new Moodle\Application\RoomService(
            $configuration,
            new Moodle\Application\NameService(),
            $this->createStub(Moodle\Domain\ModuleRepository::class),
            $this->createStub(Moodle\Domain\RoomRepository::class),
            new Matrix\Application\RoomService($this->createStub(Matrix\Application\Api::class)),
            $this->createStub(Clock\Clock::class),
        );

        $url = $roomService->urlForRoom(
            $room,
            $matrixUserId,
        );

        $expected = \sprintf(
            'https://matrix.to/#/%s',
            $room->matrixRoomId()->toString(),
        );

        self::assertSame($expected, $url);
    }

    public function testUrlForRoomThrowsModuleNotFoundExceptionWhenElementUrlIsNotBlankOrEmptyAndModuleForRoomWasNotFound(): void
    {
        $faker = self::faker();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            null,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $homeserver = $faker->domainName();

        $matrixUserId = Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->word(),
            $homeserver,
        ));

        $elementUrl = \sprintf(
            'https://%s',
            $faker->domainName(),
        );

        $configuration = Moodle\Application\Configuration::fromObject((object) [
            'access_token' => $faker->sha1(),
            'element_url' => $elementUrl,
            'homeserver_url' => \sprintf(
                'https://%s.%s',
                $faker->word(),
                $homeserver,
            ),
        ]);

        $moduleRepository = $this->createMock(Moodle\Domain\ModuleRepository::class);

        $moduleRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(self::identicalTo([
                'id' => $room->moduleId()->toInt(),
            ]))
            ->willReturn(null);

        $roomService = new Moodle\Application\RoomService(
            $configuration,
            new Moodle\Application\NameService(),
            $moduleRepository,
            $this->createStub(Moodle\Domain\RoomRepository::class),
            new Matrix\Application\RoomService($this->createStub(Matrix\Application\Api::class)),
            $this->createStub(Clock\Clock::class),
        );

        $this->expectException(Moodle\Domain\ModuleNotFound::class);

        $roomService->urlForRoom(
            $room,
            $matrixUserId,
        );
    }

    /**
     * @dataProvider provideHomeserverUrlAndHomeserverWithSameHost
     */
    public function testUrlForRoomReturnsUrlForOpeningRoomViaMatrixToWhenElementUrlIsNotBlankOrEmptyAndModuleForRoomHasMatrixToTarget(
        string $homeserverUrl,
        string $homeserver
    ): void {
        $faker = self::faker();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            null,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $matrixUserId = Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->word(),
            $homeserver,
        ));

        $module = Moodle\Domain\Module::create(
            $room->moduleId(),
            Moodle\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleName::fromString($faker->sentence()),
            Moodle\Domain\ModuleTopic::fromString($faker->sentence()),
            Moodle\Domain\ModuleTarget::matrixTo(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $elementUrl = \sprintf(
            'https://%s',
            $faker->domainName(),
        );

        $configuration = Moodle\Application\Configuration::fromObject((object) [
            'access_token' => $faker->sha1(),
            'element_url' => $elementUrl,
            'homeserver_url' => $homeserverUrl,
        ]);

        $moduleRepository = $this->createMock(Moodle\Domain\ModuleRepository::class);

        $moduleRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(self::identicalTo([
                'id' => $room->moduleId()->toInt(),
            ]))
            ->willReturn($module);

        $roomService = new Moodle\Application\RoomService(
            $configuration,
            new Moodle\Application\NameService(),
            $moduleRepository,
            $this->createStub(Moodle\Domain\RoomRepository::class),
            new Matrix\Application\RoomService($this->createStub(Matrix\Application\Api::class)),
            $this->createStub(Clock\Clock::class),
        );

        $url = $roomService->urlForRoom(
            $room,
            $matrixUserId,
        );

        $expected = \sprintf(
            'https://matrix.to/#/%s',
            $room->matrixRoomId()->toString(),
        );

        self::assertSame($expected, $url);
    }

    /**
     * @dataProvider provideHomeserverUrlAndHomeserverWithSameHost
     */
    public function testUrlForRoomReturnsUrlForOpeningRoomViaElementUrlWhenElementUrlIsNotBlankOrEmptyAndModuleForRoomHasElementUrlTarget(
        string $homeserverUrl,
        string $homeserver
    ): void {
        $faker = self::faker();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            null,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $matrixUserId = Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->word(),
            $homeserver,
        ));

        $module = Moodle\Domain\Module::create(
            $room->moduleId(),
            Moodle\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleName::fromString($faker->sentence()),
            Moodle\Domain\ModuleTopic::fromString($faker->sentence()),
            Moodle\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $elementUrl = \sprintf(
            'https://%s',
            $faker->domainName(),
        );

        $configuration = Moodle\Application\Configuration::fromObject((object) [
            'access_token' => $faker->sha1(),
            'element_url' => $elementUrl,
            'homeserver_url' => $homeserverUrl,
        ]);

        $moduleRepository = $this->createMock(Moodle\Domain\ModuleRepository::class);

        $moduleRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(self::identicalTo([
                'id' => $room->moduleId()->toInt(),
            ]))
            ->willReturn($module);

        $roomService = new Moodle\Application\RoomService(
            $configuration,
            new Moodle\Application\NameService(),
            $moduleRepository,
            $this->createStub(Moodle\Domain\RoomRepository::class),
            new Matrix\Application\RoomService($this->createStub(Matrix\Application\Api::class)),
            $this->createStub(Clock\Clock::class),
        );

        $url = $roomService->urlForRoom(
            $room,
            $matrixUserId,
        );

        $expected = \sprintf(
            '%s/#/room/%s',
            $elementUrl,
            $room->matrixRoomId()->toString(),
        );

        self::assertSame($expected, $url);
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public function provideHomeserverUrlAndHomeserverWithSameHost(): \Generator
    {
        $domainName = self::faker()->domainName();

        yield from self::provideVariationsOfHomeserverUrlAndHomeserverBasedOn(
            $domainName,
            $domainName,
        );
    }

    /**
     * @dataProvider provideHomeserverUrlAndHomeserverWithDifferentHost
     */
    public function testUrlForRoomReturnsUrlForOpeningRoomViaMatrixToWhenElementUrlIsNotBlankOrEmptyAndMatrixUserIdHasDifferentHomeserverThanHomeserverUrl(
        string $homeserverUrl,
        string $homeserver
    ): void {
        $faker = self::faker();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            null,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $matrixUserId = Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->word(),
            $homeserver,
        ));

        $elementUrl = \sprintf(
            'https://%s',
            $faker->domainName(),
        );

        $configuration = Moodle\Application\Configuration::fromObject((object) [
            'access_token' => $faker->sha1(),
            'element_url' => $elementUrl,
            'homeserver_url' => $homeserverUrl,
        ]);

        $roomService = new Moodle\Application\RoomService(
            $configuration,
            new Moodle\Application\NameService(),
            $this->createStub(Moodle\Domain\ModuleRepository::class),
            $this->createStub(Moodle\Domain\RoomRepository::class),
            new Matrix\Application\RoomService($this->createStub(Matrix\Application\Api::class)),
            $this->createStub(Clock\Clock::class),
        );

        $url = $roomService->urlForRoom(
            $room,
            $matrixUserId,
        );

        $expected = \sprintf(
            'https://matrix.to/#/%s',
            $room->matrixRoomId()->toString(),
        );

        self::assertSame($expected, $url);
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public function provideHomeserverUrlAndHomeserverWithDifferentHost(): \Generator
    {
        $faker = self::faker()->unique();

        yield from self::provideVariationsOfHomeserverUrlAndHomeserverBasedOn(
            $faker->domainName(),
            $faker->domainName(),
        );
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    private static function provideVariationsOfHomeserverUrlAndHomeserverBasedOn(
        string $homeserverUrlDomain,
        string $homeserver
    ): \Generator {
        $faker = self::faker();

        $protocols = [
            'https' => 'https://',
            'http' => 'http://',
        ];

        $subdomains = [
            'with-subdomain' => \sprintf(
                '%s.',
                $faker->word(),
            ),
            'without-subdomain' => '',
        ];

        $paths = [
            'with-path' => \sprintf(
                '/%s',
                $faker->word(),
            ),
            'without-path' => '',
        ];

        $slashes = [
            'with-slash' => '/',
            'without-slash' => '',
        ];

        $variations = [
            'regular-case' => static function (string $value): string {
                return $value;
            },
            'mixed-case' => static function (string $value) use ($faker): string {
                return \implode(
                    '',
                    \array_map(static function (string $character) use ($faker): string {
                        if ($faker->boolean()) {
                            return \mb_strtoupper($character);
                        }

                        return \mb_strtolower($character);
                    }, \str_split($value)),
                );
            },
        ];

        foreach ($protocols as $protocolKey => $protocol) {
            foreach ($subdomains as $subdomainKey => $subdomain) {
                foreach ($variations as $variationKey => $variation) {
                    foreach ($paths as $pathKey => $path) {
                        foreach ($slashes as $slashKey => $slash) {
                            $key = \sprintf(
                                '%s-%s-%s-%s-%s',
                                $protocolKey,
                                $subdomainKey,
                                $variationKey,
                                $pathKey,
                                $slashKey,
                            );

                            $parts = [
                                $protocol,
                                $subdomain,
                                $variation($homeserverUrlDomain),
                                $path,
                                $slash,
                            ];

                            $homeserverUrl = \implode(
                                '',
                                $parts,
                            );

                            yield $key => [
                                $homeserverUrl,
                                $homeserver,
                            ];
                        }
                    }
                }
            }
        }
    }
}
