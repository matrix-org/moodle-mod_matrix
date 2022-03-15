<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Moodle;

/**
 * @see https://docs.moodle.org/dev/Restore_API
 * @see https://docs.moodle.org/dev/Restore_API#Task_class
 */
final class restore_matrix_activity_structure_step extends restore_activity_structure_step
{
    protected function define_structure()
    {
        return $this->prepare_activity_structure([
            new restore_path_element(
                'matrix',
                '/activity/matrix',
            ),
            new restore_path_element(
                'room',
                '/activity/matrix/rooms/room',
            ),
        ]);
    }

    /**
     * @see https://github.com/moodle/moodle/blob/v3.9.5/backup/util/structure/restore_path_element.class.php#L82-L84
     * @see https://github.com/moodle/moodle/blob/v3.9.5/backup/util/structure/restore_path_element.class.php#L114-L116
     *
     * @param mixed $data
     */
    protected function process_matrix($data): void
    {
        global $DB;

        $data = (object) $data;

        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record(
            'matrix',
            $data,
        );

        $this->apply_activity_instance($newitemid);
    }

    /**
     * @see https://github.com/moodle/moodle/blob/v3.9.5/backup/util/structure/restore_path_element.class.php#L82-L84
     * @see https://github.com/moodle/moodle/blob/v3.9.5/backup/util/structure/restore_path_element.class.php#L114-L116
     *
     * @param mixed $data
     */
    protected function process_room($data): void
    {
        global $DB;

        $data = (object) $data;

        $oldId = $data->id;

        $data->module_id = $this->get_new_parentid('matrix');

        $newItemId = $DB->insert_record(
            'matrix_rooms',
            $data,
        );

        $this->set_mapping(
            'module_id',
            $oldId,
            $newItemId,
        );
    }
}
