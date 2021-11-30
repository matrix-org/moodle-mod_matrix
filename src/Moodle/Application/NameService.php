<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Application;

use mod_matrix\Moodle;

final class NameService
{
    public function createForGroupCourseAndModule(
        Moodle\Domain\Group $group,
        Moodle\Domain\Course $course,
        Moodle\Domain\Module $module
    ): string {
        return \sprintf(
            '%s: %s (%s)',
            $group->name()->toString(),
            $course->name()->toString(),
            $module->name()->toString(),
        );
    }

    public function createForCourseAndModule(
        Moodle\Domain\Course $course,
        Moodle\Domain\Module $module
    ): string {
        return \sprintf(
            '%s (%s)',
            $course->name()->toString(),
            $module->name()->toString(),
        );
    }
}
