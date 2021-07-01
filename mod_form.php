<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || exit();

require_once $CFG->dirroot . '/course/moodleform_mod.php';

require_once $CFG->dirroot . '/mod/matrix/lib.php';

class mod_matrix_mod_form extends moodleform_mod
{
    protected function definition()
    {
        global $CFG, $DB, $OUTPUT;

        // We don't have any config options
        $this->apply_admin_defaults();

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
