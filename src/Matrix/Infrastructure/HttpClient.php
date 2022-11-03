<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Matrix\Infrastructure;

interface HttpClient
{
    /**
     * @throws \RuntimeException
     */
    public function get(string $path);

    /**
     * @throws \RuntimeException
     */
    public function post(
        string $path,
        array $body = []
    );

    /**
     * @throws \RuntimeException
     */
    public function put(
        string $path,
        array $body = []
    );
}
