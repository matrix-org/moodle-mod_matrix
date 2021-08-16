<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

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
        $this->define(Matrix\Api::class, static function (self $container): Matrix\Api {
            $configuration = $container->configuration();

            return new Matrix\Api(
                $configuration->hsUrl(),
                $configuration->accessToken()
            );
        });

        $this->define(Matrix\Configuration::class, static function (): Matrix\Configuration {
            $object = get_config('mod_matrix');

            return Matrix\Configuration::fromObject($object);
        });

        $this->define(Matrix\Repository\RoomRepository::class, static function (): Matrix\Repository\RoomRepository {
            global $DB;

            return new Matrix\Repository\RoomRepository($DB);
        });

        $this->define(Matrix\Service::class, static function (self $container): Matrix\Service {
            return new Matrix\Service(
                $container->api(),
                $container->configuration(),
                $container->roomRepository()
            );
        });
    }

    public static function instance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function api(): Matrix\Api
    {
        return $this->resolve(Matrix\Api::class);
    }

    public function configuration(): Matrix\Configuration
    {
        return $this->resolve(Matrix\Configuration::class);
    }

    public function roomRepository(): Matrix\Repository\RoomRepository
    {
        return $this->resolve(Matrix\Repository\RoomRepository::class);
    }

    public function service(): Matrix\Service
    {
        return $this->resolve(Matrix\Service::class);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function define(string $identifier, \Closure $closure): void
    {
        if (array_key_exists($identifier, $this->definitions)) {
            throw new \InvalidArgumentException(sprintf(
                'A service definition for identifier "%s" has already been registered.',
                $identifier
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
        if (!array_key_exists($identifier, $this->definitions)) {
            throw new \InvalidArgumentException(sprintf(
                'A service definition for identifier "%s" has not been registered.',
                $identifier
            ));
        }

        if (!array_key_exists($identifier, $this->services)) {
            $definition = $this->definitions[$identifier];

            $service = $definition($this);

            $this->services[$identifier] = $service;
        }

        return $this->services[$identifier];
    }
}
