<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Infrastructure\Form;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

global $CFG;

require_once $CFG->libdir . '/formslib.php';

/**
 * @see https://docs.moodle.org/dev/Form_API#Usage
 */
final class EditMatrixUserIdForm extends \moodleform
{
    public function validation(
        $data,
        $files
    ): array {
        $element = Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME;

        if (!\array_key_exists($element, $data)) {
            return [
                $element => get_string(
                    Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_REQUIRED,
                    Plugin\Application\Plugin::NAME,
                ),
            ];
        }

        $error = \implode(' ', [
            get_string(
                Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_INVALID,
                Plugin\Application\Plugin::NAME,
            ),
            get_string(
                Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_HELP,
                Plugin\Application\Plugin::NAME,
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
                Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_HEADER,
                Plugin\Application\Plugin::NAME,
            ),
        );

        $this->addMatrixUserIdElement();
    }

    private function addMatrixUserIdElement(): void
    {
        $textElementName = Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME;

        $this->_form->addElement(
            'text',
            $textElementName,
            get_string(
                Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME,
                Plugin\Application\Plugin::NAME,
            ),
            [
                'id' => $textElementName,
                'size' => 30,
            ],
        );

        $this->_form->setType(
            $textElementName,
            PARAM_TEXT,
        );

        $this->_form->addHelpButton(
            $textElementName,
            Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME,
            Plugin\Application\Plugin::NAME,
        );

        $this->_form->addRule(
            $textElementName,
            get_string(
                Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_REQUIRED,
                Plugin\Application\Plugin::NAME,
            ),
            'required',
        );

        if (!\is_array($this->_customdata)) {
            return;
        }

        if (!\array_key_exists('matrixUserIdSuggestions', $this->_customdata)) {
            return;
        }

        $matrixUserIdSuggestions = $this->_customdata['matrixUserIdSuggestions'];

        if (!\is_array($matrixUserIdSuggestions)) {
            return;
        }

        $selectElementName = \sprintf(
            '%s_suggestion',
            Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME,
        );

        $values = \array_map(static function (Matrix\Domain\UserId $userId): string {
            return $userId->toString();
        }, $matrixUserIdSuggestions);

        $options = \array_merge(
            [
                '' => get_string(
                    Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION_DEFAULT,
                    Plugin\Application\Plugin::NAME,
                ),
            ],
            \array_combine(
                $values,
                $values,
            ),
        );

        $this->_form->addElement(
            'select',
            $selectElementName,
            get_string(
                Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION,
                Plugin\Application\Plugin::NAME,
            ),
            $options,
            [
                'id' => $selectElementName,
                'onchange' => \sprintf(
                    'javascript:document.getElementById("%s").value = document.getElementById("%s").value',
                    $textElementName,
                    $selectElementName,
                ),
            ],
        );

        $this->_form->addHelpButton(
            $selectElementName,
            Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION,
            Plugin\Application\Plugin::NAME,
        );
    }
}
