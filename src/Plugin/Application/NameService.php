<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Application;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class NameService
{
    public function forGroupCourseAndModule(
        Moodle\Domain\GroupName $groupName,
        Moodle\Domain\CourseShortName $courseShortName,
        Plugin\Domain\ModuleName $moduleName
    ): Matrix\Domain\RoomName {
        return Matrix\Domain\RoomName::fromString(\sprintf(
            '%s (%s, %s)',
            $courseShortName->toString(),
            $moduleName->toString(),
            $groupName->toString(),
        ));
    }

    public function forCourseAndModule(
        Moodle\Domain\CourseShortName $courseShortName,
        Plugin\Domain\ModuleName $moduleName
    ): Matrix\Domain\RoomName {
        return Matrix\Domain\RoomName::fromString(\sprintf(
            '%s (%s)',
            $courseShortName->toString(),
            $moduleName->toString(),
        ));
    }
}
