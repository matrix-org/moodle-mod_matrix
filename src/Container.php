<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
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

        $this->define(Plugin\Application\Configuration::class, static function (): Plugin\Application\Configuration {
            $object = get_config('mod_matrix');

            return Plugin\Application\Configuration::fromObject($object);
        });

        $this->define(Matrix\Application\RoomService::class, static function (self $container): Matrix\Application\RoomService {
            return new Matrix\Application\RoomService($container->api());
        });

        $this->define(Plugin\Application\ModuleService::class, static function (self $container): Plugin\Application\ModuleService {
            return new Plugin\Application\ModuleService(
                $container->moduleRepository(),
                $container->clock(),
            );
        });

        $this->define(Plugin\Application\NameService::class, static function (): Plugin\Application\NameService {
            return new Plugin\Application\NameService();
        });

        $this->define(Plugin\Application\RoomService::class, static function (self $container): Plugin\Application\RoomService {
            return new Plugin\Application\RoomService(
                $container->configuration(),
                $container->nameService(),
                $container->moduleRepository(),
                $container->roomRepository(),
                $container->matrixRoomService(),
                $container->clock(),
            );
        });

        $this->define(Moodle\Domain\CourseRepository::class, static function (): Moodle\Domain\CourseRepository {
            return new Moodle\Infrastructure\MoodleFunctionBasedCourseRepository();
        });

        $this->define(Moodle\Domain\GroupRepository::class, static function (): Moodle\Domain\GroupRepository {
            return new Moodle\Infrastructure\MoodleFunctionBasedGroupRepository();
        });

        $this->define(Plugin\Domain\MatrixUserIdLoader::class, static function (): Plugin\Domain\MatrixUserIdLoader {
            return new Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader();
        });

        $this->define(Plugin\Domain\ModuleRepository::class, static function (self $container): Plugin\Domain\ModuleRepository {
            return new Plugin\Infrastructure\DatabaseBasedModuleRepository(
                $container->database(),
                new Plugin\Infrastructure\ModuleNormalizer(),
            );
        });

        $this->define(Plugin\Domain\RoomRepository::class, static function (self $container): Plugin\Domain\RoomRepository {
            return new Plugin\Infrastructure\DatabaseBasedRoomRepository(
                $container->database(),
                new Plugin\Infrastructure\RoomNormalizer(),
            );
        });

        $this->define(Plugin\Domain\UserRepository::class, static function (self $container): Plugin\Domain\UserRepository {
            return new Plugin\Infrastructure\MoodleFunctionBasedUserRepository($container->matrixUserIdLoader());
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

    public function configuration(): Plugin\Application\Configuration
    {
        return $this->resolve(Plugin\Application\Configuration::class);
    }

    public function moodleCourseRepository(): Moodle\Domain\CourseRepository
    {
        return $this->resolve(Moodle\Domain\CourseRepository::class);
    }

    public function moodleGroupRepository(): Moodle\Domain\GroupRepository
    {
        return $this->resolve(Moodle\Domain\GroupRepository::class);
    }

    public function matrixUserIdLoader(): Plugin\Domain\MatrixUserIdLoader
    {
        return $this->resolve(Plugin\Domain\MatrixUserIdLoader::class);
    }

    public function moduleRepository(): Plugin\Domain\ModuleRepository
    {
        return $this->resolve(Plugin\Domain\ModuleRepository::class);
    }

    public function moduleService(): Plugin\Application\ModuleService
    {
        return $this->resolve(Plugin\Application\ModuleService::class);
    }

    public function nameService(): Plugin\Application\NameService
    {
        return $this->resolve(Plugin\Application\NameService::class);
    }

    public function roomRepository(): Plugin\Domain\RoomRepository
    {
        return $this->resolve(Plugin\Domain\RoomRepository::class);
    }

    public function roomService(): Plugin\Application\RoomService
    {
        return $this->resolve(Plugin\Application\RoomService::class);
    }

    public function userRepository(): Plugin\Domain\UserRepository
    {
        return $this->resolve(Plugin\Domain\UserRepository::class);
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
