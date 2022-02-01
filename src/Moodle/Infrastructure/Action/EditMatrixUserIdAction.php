<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure\Action;

use core\output;
use mod_matrix\Moodle;

final class EditMatrixUserIdAction
{
    private $page;
    private $renderer;

    public function __construct(
        \moodle_page $page,
        \core_renderer $renderer
    ) {
        $this->page = $page;
        $this->renderer = $renderer;
    }

    public function handle(\stdClass $user): void
    {
        $matrixUserIdForm = new Moodle\Infrastructure\Form\EditMatrixUserIdForm($this->page->url->out(true));

        if (!$matrixUserIdForm->is_submitted()) {
            echo $this->renderer->heading(get_string(
                Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_HEADER,
                Moodle\Application\Plugin::NAME,
            ));

            echo $this->renderer->notification(
                get_string(
                    Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_WARNING_NO_MATRIX_USER_ID,
                    Moodle\Application\Plugin::NAME,
                ),
                output\notification::NOTIFY_WARNING,
            );

            $matrixUserIdForm->display();

            echo $this->renderer->footer();

            return;
        }

        if (!$matrixUserIdForm->is_validated()) {
            echo $this->renderer->heading(get_string(
                Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_HEADER,
                Moodle\Application\Plugin::NAME,
            ));

            $matrixUserIdForm->display();

            echo $this->renderer->footer();

            return;
        }

        $data = $matrixUserIdForm->get_data();

        $name = Moodle\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME;

        profile_save_custom_fields($user->id, [
            Moodle\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME => $data->{$name},
        ]);

        redirect($this->page->url);
    }
}
