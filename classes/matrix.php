<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

final class matrix
{
    /**
     * The default client-server API URL.
     */
    public const DEFAULT_HS_URL = 'https://matrix-client.matrix.org';

    /**
     * The default access token on the given homeserver.
     */
    public const DEFAULT_ACCESS_TOKEN = '';

    /**
     * The default Element Web URL.
     */
    public const DEFAULT_ELEMENT_URL = '';
}
