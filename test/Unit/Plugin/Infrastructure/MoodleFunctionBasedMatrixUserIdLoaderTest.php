<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Plugin;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader
 */
final class MoodleFunctionBasedMatrixUserIdLoaderTest extends Framework\TestCase
{
    public function testConstants(): void
    {
        self::assertSame('matrix_user_id', Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME);
    }
}
