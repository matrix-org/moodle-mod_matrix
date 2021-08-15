<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\twitter;

final class bootstrap
{
    /**
     * Renders a Twitter Bootstrap alert.
     *
     * @throws \InvalidArgumentException
     */
    public static function alert(string $type, string $content): string
    {
        $types = [
            'danger',
            'dark',
            'info',
            'light',
            'primary',
            'secondary',
            'success',
            'warning',
        ];

        if (!in_array($type, $types, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Type needs to be one "%s", got "%s" instead.',
                implode('", "', $types),
                $type
            ));
        }

        return <<<TXT
<div class="alert alert-${type}">
    ${content}
</div>
TXT;
    }
}
