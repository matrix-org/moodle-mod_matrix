<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Moodle;

/**
 * @see https://docs.moodle.org/dev/Backup_API
 * @see https://docs.moodle.org/dev/Backup_API#Backup_structure_step_class
 * @see https://docs.moodle.org/dev/Backup_2.0_for_developers#Settings.2C_Steps_and_Tasks
 */
final class backup_matrix_activity_structure_step extends backup_activity_structure_step
{
    protected function define_structure(): backup_nested_element
    {
        $matrix = new backup_nested_element(
            'matrix',
            [
                'id',
            ],
            [
                'type',
                'name',
                'topic',
                'target',
                'course',
                'section',
                'timecreated',
                'timemodified',
            ],
        );

        $matrix->set_source_table(
            'matrix',
            [
                'id' => backup::VAR_ACTIVITYID,
            ],
        );

        $rooms = new backup_nested_element('rooms');

        $room = new backup_nested_element(
            'room',
            [
                'id',
            ],
            [
                'module_id',
                'group_id',
                'room_id',
                'timecreated',
                'timeupdated',
            ],
        );

        $room->set_source_table(
            'matrix_rooms',
            [
                'module_id' => backup::VAR_ACTIVITYID,
            ],
            'id ASC',
        );

        $rooms->add_child($room);

        $matrix->add_child($rooms);

        return $this->prepare_activity_structure($matrix);
    }
}
