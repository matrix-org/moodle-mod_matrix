<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

require_once $CFG->dirroot . '/course/moodleform_mod.php';

/**
 * @see https://docs.moodle.org/dev/Activity_modules#mod_form.php
 * @see https://docs.moodle.org/dev/Form_API
 * @see https://github.com/moodle/moodle/blob/02a2e649e92d570c7fa735bf05f69b588036f761/course/modedit.php#L142-L147
 */
final class mod_matrix_mod_form extends moodleform_mod
{
    protected function definition(): void
    {
        // We don't have any config options
        $this->apply_admin_defaults();

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
