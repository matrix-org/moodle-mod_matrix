<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Util\Matrix\Domain;

use Faker\Generator;
use mod_matrix\Matrix;

final class UserIdFactory
{
    public static function create(Generator $faker): Matrix\Domain\UserId
    {
        return Matrix\Domain\UserId::fromString(\sprintf(
            '@%s:%s',
            $faker->word(),
            $faker->domainName(),
        ));
    }
}
