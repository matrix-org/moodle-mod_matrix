<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

use Ergebnis\Clock;

final class Container
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var array<string, \Closure>
     */
    private $definitions = [];

    /**
     * @var array<string, object>
     */
    private $services = [];

    private function __construct()
    {
        $this->define(Clock\Clock::class, static function (): Clock\Clock {
            $timezone = new \DateTimeZone(\date_default_timezone_get());

            return new Clock\SystemClock($timezone);
        });

        $this->define(Matrix\Application\Api::class, static function (self $container): Matrix\Application\Api {
            $configuration = $container->configuration();

            return new Matrix\Infrastructure\HttpClientBasedApi(new Matrix\Infrastructure\CurlBasedHttpClient(
                $configuration->homeserverUrl(),
                $configuration->accessToken(),
            ));
        });

        $this->define(Matrix\Application\Configuration::class, static function (): Matrix\Application\Configuration {
            $object = get_config('mod_matrix');

            return Matrix\Application\Configuration::fromObject($object);
        });

        $this->define(Matrix\Application\RoomService::class, static function (self $container): Matrix\Application\RoomService {
            return new Matrix\Application\RoomService(
                $container->api(),
                $container->configuration(),
            );
        });

        $this->define(Moodle\Application\ModuleService::class, static function (self $container): Moodle\Application\ModuleService {
            return new Moodle\Application\ModuleService(
                $container->moodleModuleRepository(),
                $container->clock(),
            );
        });

        $this->define(Moodle\Domain\CourseRepository::class, static function (): Moodle\Domain\CourseRepository {
            return new Moodle\Infrastructure\MoodleFunctionBasedCourseRepository();
        });

        $this->define(Moodle\Domain\GroupRepository::class, static function (): Moodle\Domain\GroupRepository {
            return new Moodle\Infrastructure\MoodleFunctionBasedGroupRepository();
        });

        $this->define(Moodle\Domain\ModuleRepository::class, static function (self $container): Moodle\Domain\ModuleRepository {
            return new Moodle\Infrastructure\DatabaseBasedModuleRepository(
                $container->database(),
                new Moodle\Infrastructure\ModuleNormalizer(),
            );
        });

        $this->define(Moodle\Domain\RoomRepository::class, static function (self $container): Moodle\Domain\RoomRepository {
            return new Moodle\Infrastructure\DatabaseBasedRoomRepository(
                $container->database(),
                new Moodle\Infrastructure\RoomNormalizer(),
            );
        });

        $this->define(Moodle\Domain\UserRepository::class, static function (): Moodle\Domain\UserRepository {
            return new Moodle\Infrastructure\MoodleFunctionBasedUserRepository();
        });

        $this->define(\moodle_database::class, static function (): \moodle_database {
            global $DB;

            if (!$DB instanceof \moodle_database) {
                throw new \RuntimeException(\sprintf(
                    'Expected global variable $DB to reference an instance of "%s", got "%s" instead.',
                    \moodle_database::class,
                    \is_object($DB) ? \get_class($DB) : \gettype($DB),
                ));
            }

            return $DB;
        });
    }

    public static function instance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function api(): Matrix\Application\Api
    {
        return $this->resolve(Matrix\Application\Api::class);
    }

    public function configuration(): Matrix\Application\Configuration
    {
        return $this->resolve(Matrix\Application\Configuration::class);
    }

    public function clock(): Clock\Clock
    {
        return $this->resolve(Clock\Clock::class);
    }

    public function database(): \moodle_database
    {
        return $this->resolve(\moodle_database::class);
    }

    public function matrixRoomService(): Matrix\Application\RoomService
    {
        return $this->resolve(Matrix\Application\RoomService::class);
    }

    public function moodleCourseRepository(): Moodle\Domain\CourseRepository
    {
        return $this->resolve(Moodle\Domain\CourseRepository::class);
    }

    public function moodleGroupRepository(): Moodle\Domain\GroupRepository
    {
        return $this->resolve(Moodle\Domain\GroupRepository::class);
    }

    public function moodleModuleRepository(): Moodle\Domain\ModuleRepository
    {
        return $this->resolve(Moodle\Domain\ModuleRepository::class);
    }

    public function moodleModuleService(): Moodle\Application\ModuleService
    {
        return $this->resolve(Moodle\Application\ModuleService::class);
    }

    public function moodleRoomRepository(): Moodle\Domain\RoomRepository
    {
        return $this->resolve(Moodle\Domain\RoomRepository::class);
    }

    public function moodleUserRepository(): Moodle\Domain\UserRepository
    {
        return $this->resolve(Moodle\Domain\UserRepository::class);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function define(string $identifier, \Closure $closure): void
    {
        if (\array_key_exists($identifier, $this->definitions)) {
            throw new \InvalidArgumentException(\sprintf(
                'A service definition for identifier "%s" has already been registered.',
                $identifier,
            ));
        }

        $container = $this;

        $this->definitions[$identifier] = static function () use ($closure, $container) {
            return $closure($container);
        };
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function resolve(string $identifier)
    {
        if (!\array_key_exists($identifier, $this->definitions)) {
            throw new \InvalidArgumentException(\sprintf(
                'A service definition for identifier "%s" has not been registered.',
                $identifier,
            ));
        }

        if (!\array_key_exists($identifier, $this->services)) {
            $definition = $this->definitions[$identifier];

            $service = $definition($this);

            $this->services[$identifier] = $service;
        }

        return $this->services[$identifier];
    }
}
