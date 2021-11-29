<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Moodle;

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
        $this->addElementsForBasicSettings();

        // We don't have any config options
        $this->apply_admin_defaults();

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    private function addElementsForBasicSettings(): void
    {
        $this->_form->addElement(
            'header',
            'mod_form_basic_settings_header',
            get_string(
                Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_HEADER,
                Moodle\Application\Plugin::NAME,
            ),
        );

        $this->addNameElement();
    }

    private function addNameElement(): void
    {
        $elementName = 'name';

        $this->_form->addElement(
            'text',
            $elementName,
            get_string(
                Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_NAME,
                Moodle\Application\Plugin::NAME,
            ),
            [
                'maxlength' => Moodle\Domain\Name::LENGTH_MAX,
            ],
        );

        $this->_form->setDefault(
            $elementName,
            get_string(
                Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT,
                Moodle\Application\Plugin::NAME,
            ),
        );

        $this->_form->setType(
            $elementName,
            PARAM_TEXT,
        );

        $this->_form->addHelpButton(
            $elementName,
            'mod_form_basic_settings_name',
            Moodle\Application\Plugin::NAME,
        );

        $this->_form->addRule(
            $elementName,
            get_string(
                Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED,
                Moodle\Application\Plugin::NAME,
            ),
            'required',
        );

        $this->_form->addRule(
            $elementName,
            get_string(
                Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH,
                Moodle\Application\Plugin::NAME,
            ),
            'maxlength',
            Moodle\Domain\Name::LENGTH_MAX,
        );
    }
}
