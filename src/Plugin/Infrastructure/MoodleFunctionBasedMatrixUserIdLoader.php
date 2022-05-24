<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Plugin;

final class MoodleFunctionBasedMatrixUserIdLoader implements Plugin\Domain\MatrixUserIdLoader
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

        $value = $user->profile[self::USER_PROFILE_FIELD_NAME];

        if (!\is_string($value)) {
            return null;
        }

        try {
            $userId = Matrix\Domain\UserId::fromString($value);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }

        return $userId;
    }
}
