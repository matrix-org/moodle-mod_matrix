<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
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
