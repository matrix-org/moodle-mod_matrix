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
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function request(
        string $method,
        string $path,
        array $qs = [],
        array $body = []
    );
}
