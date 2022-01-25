<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Moodle;

final class MoodleFunctionBasedMatrixUserIdLoader implements Moodle\Domain\MatrixUserIdLoader
{
    public const USER_PROFILE_FIELD_NAME = 'matrix_user_id';

    public function load(object $user): ?Matrix\Domain\UserId
    {
        profile_load_custom_fields($user);

        if (!\property_exists($user, 'profile')) {
            return null;
        }

        if (!\is_array($user->profile)) {
            return null;
        }

        if (!\array_key_exists(self::USER_PROFILE_FIELD_NAME, $user->profile)) {
            return null;
        }

        $matrixUserId = $user->profile[self::USER_PROFILE_FIELD_NAME];

        if (!\is_string($matrixUserId)) {
            return null;
        }

        if ('' === \trim($matrixUserId)) {
            return null;
        }

        return Matrix\Domain\UserId::fromString($matrixUserId);
    }
}
