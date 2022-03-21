<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Container;
use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

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
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_HEADER,
                Plugin\Application\Plugin::NAME,
            ),
        );

        $this->addNameElement();
        $this->addTopicElement();
        $this->addTargetElement();
    }

    private function addNameElement(): void
    {
        $elementName = 'name';

        $this->_form->addElement(
            'text',
            $elementName,
            get_string(
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_NAME,
                Plugin\Application\Plugin::NAME,
            ),
            [
                'maxlength' => Plugin\Domain\ModuleName::LENGTH_MAX,
            ],
        );

        $this->_form->setDefault(
            $elementName,
            get_string(
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT,
                Plugin\Application\Plugin::NAME,
            ),
        );

        $this->_form->setType(
            $elementName,
            PARAM_TEXT,
        );

        $this->_form->addHelpButton(
            $elementName,
            Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME,
            Plugin\Application\Plugin::NAME,
        );

        $this->_form->addRule(
            $elementName,
            get_string(
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED,
                Plugin\Application\Plugin::NAME,
            ),
            'required',
        );

        $this->_form->addRule(
            $elementName,
            get_string(
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH,
                Plugin\Application\Plugin::NAME,
            ),
            'maxlength',
            Plugin\Domain\ModuleName::LENGTH_MAX,
        );
    }

    private function addTopicElement(): void
    {
        $elementName = 'topic';

        $this->_form->addElement(
            'textarea',
            $elementName,
            get_string(
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_NAME,
                Plugin\Application\Plugin::NAME,
            ),
            [
                'cols' => 50,
                'rows' => 3,
                'wrap' => 'virtual',
            ],
        );

        $this->_form->setType(
            $elementName,
            PARAM_TEXT,
        );
    }

    private function addTargetElement(): void
    {
        $configuration = Container::instance()->configuration();

        if ($configuration->elementUrl()->toString() === '') {
            return;
        }

        $elementName = 'target';
        $groupName = 'targetGroup';

        $this->_form->addGroup(
            [
                $this->_form->createElement(
                    'radio',
                    $elementName,
                    '',
                    get_string(
                        Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_ELEMENT_URL,
                        Plugin\Application\Plugin::NAME,
                    ),
                    Plugin\Domain\ModuleTarget::elementUrl()->toString(),
                    [],
                ),
                $this->_form->createElement(
                    'radio',
                    $elementName,
                    '',
                    get_string(
                        Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_MATRIX_TO,
                        Plugin\Application\Plugin::NAME,
                    ),
                    Plugin\Domain\ModuleTarget::matrixTo()->toString(),
                    [],
                ),
            ],
            $groupName,
            get_string(
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_NAME,
                Plugin\Application\Plugin::NAME,
            ),
            null,
            false,
        );

        $this->_form->addRule(
            $groupName,
            get_string(
                Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_ERROR_REQUIRED,
                Plugin\Application\Plugin::NAME,
            ),
            'required',
        );
    }
}
