<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Application;

use mod_matrix\Matrix;
use mod_matrix\Moodle;

final class NameService
{
    public function createForGroupCourseAndModule(
        Moodle\Domain\GroupName $groupName,
        Moodle\Domain\CourseShortName $courseShortName,
        Moodle\Domain\ModuleName $moduleName
    ): Matrix\Domain\RoomName {
        return Matrix\Domain\RoomName::fromString(\sprintf(
            '%s: %s (%s)',
            $groupName->toString(),
            $courseShortName->toString(),
            $moduleName->toString(),
        ));
    }

    public function createForCourseAndModule(
        Moodle\Domain\CourseShortName $courseShortName,
        Moodle\Domain\ModuleName $moduleName
    ): Matrix\Domain\RoomName {
        return Matrix\Domain\RoomName::fromString(\sprintf(
            '%s (%s)',
            $courseShortName->toString(),
            $moduleName->toString(),
        ));
    }
}
