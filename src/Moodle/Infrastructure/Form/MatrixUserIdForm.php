<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure\Form;

use mod_matrix\Matrix;
use mod_matrix\Moodle;

global $CFG;

require_once $CFG->libdir . '/formslib.php';

/**
 * @see https://docs.moodle.org/dev/Form_API#Usage
 */
final class MatrixUserIdForm extends \moodleform
{
    public function validation(
        $data,
        $files
    ): array {
        $element = Moodle\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME;

        if (!\array_key_exists($element, $data)) {
            return [
                $element => get_string(
                    Moodle\Infrastructure\Internationalization::VIEW_MATRIX_USER_ID_FORM_NAME_ERROR_REQUIRED,
                    Moodle\Application\Plugin::NAME,
                ),
            ];
        }

        $error = \implode(' ', [
            get_string(
                Moodle\Infrastructure\Internationalization::VIEW_MATRIX_USER_ID_FORM_NAME_ERROR_INVALID,
                Moodle\Application\Plugin::NAME,
            ),
            get_string(
                Moodle\Infrastructure\Internationalization::VIEW_MATRIX_USER_ID_FORM_NAME_HELP,
                Moodle\Application\Plugin::NAME,
            ),
        ]);

        if (!\is_string($data[$element])) {
            return [
                $element => $error,
            ];
        }

        try {
            Matrix\Domain\UserId::fromString($data[$element]);
        } catch (\InvalidArgumentException $exception) {
            return [
                $element => $error,
            ];
        }

        return [];
    }

    protected function definition(): void
    {
        $this->addElementsForBasicSettings();

        $this->add_action_buttons(false);
    }

    private function addElementsForBasicSettings(): void
    {
        $this->_form->addElement(
            'header',
            'mod_form_basic_settings_header',
            get_string(
                Moodle\Infrastructure\Internationalization::VIEW_MATRIX_USER_ID_FORM_HEADER,
                Moodle\Application\Plugin::NAME,
            ),
        );

        $this->addMatrixUserIdElement();
    }

    private function addMatrixUserIdElement(): void
    {
        $elementName = Moodle\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME;

        $this->_form->addElement(
            'text',
            $elementName,
            get_string(
                Moodle\Infrastructure\Internationalization::VIEW_MATRIX_USER_ID_FORM_NAME,
                Moodle\Application\Plugin::NAME,
            ),
        );

        $this->_form->setType(
            $elementName,
            PARAM_TEXT,
        );

        $this->_form->addHelpButton(
            $elementName,
            Moodle\Infrastructure\Internationalization::VIEW_MATRIX_USER_ID_FORM_NAME,
            Moodle\Application\Plugin::NAME,
        );

        $this->_form->addRule(
            $elementName,
            get_string(
                Moodle\Infrastructure\Internationalization::VIEW_MATRIX_USER_ID_FORM_NAME_ERROR_REQUIRED,
                Moodle\Application\Plugin::NAME,
            ),
            'required',
        );
    }
}
