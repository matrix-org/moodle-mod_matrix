<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

final class container
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
        $this->define(matrix\api::class, static function (self $container): matrix\api {
            $configuration = $container->configuration();

            return new matrix\api(
                $configuration->hsUrl(),
                $configuration->accessToken()
            );
        });

        $this->define(matrix\configuration::class, static function (): matrix\configuration {
            $object = get_config('mod_matrix');

            return matrix\configuration::fromObject($object);
        });

        $this->define(matrix\service::class, static function (self $container): matrix\service {
            return new matrix\service(
                $container->api(),
                $container->configuration()
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

    public function api(): matrix\api
    {
        return $this->resolve(matrix\api::class);
    }

    public function configuration(): matrix\configuration
    {
        return $this->resolve(matrix\configuration::class);
    }

    public function service(): matrix\service
    {
        return $this->resolve(matrix\service::class);
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
