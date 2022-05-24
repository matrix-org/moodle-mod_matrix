<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
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
