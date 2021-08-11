<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

require_once __DIR__ . '/../vendor/autoload.php';

final class container
{
    /**
     * @var self
     */
    private static $instance;

    private function __construct()
    {
    }

    public static function instance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function configuration(): configuration
    {
        $object = get_config('mod_matrix');

        return configuration::fromObject($object);
    }
}
