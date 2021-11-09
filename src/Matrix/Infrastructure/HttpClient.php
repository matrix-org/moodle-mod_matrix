<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Infrastructure;

interface HttpClient
{
    /**
     * @throws \RuntimeException
     */
    public function get(
        string $path,
        array $query = []
    );

    /**
     * @throws \RuntimeException
     */
    public function post(
        string $path,
        array $query = [],
        array $body = []
    );

    /**
     * @throws \RuntimeException
     */
    public function put(
        string $path,
        array $query = [],
        array $body = []
    );
}
