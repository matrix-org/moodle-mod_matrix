<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure\Action;

use core\output;
use mod_matrix\Matrix;
use mod_matrix\Moodle;

final class EditMatrixUserIdAction
{
    private $page;
    private $renderer;
    private $moodleConfiguration;

    public function __construct(
        \moodle_page $page,
        \core_renderer $renderer,
        Moodle\Application\Configuration $moodleConfiguration
    ) {
        $this->page = $page;
        $this->renderer = $renderer;
        $this->moodleConfiguration = $moodleConfiguration;
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

            $matrixUserIdSuggestions = $this->matrixUserIdSuggestions($user);

            if ([] !== $matrixUserIdSuggestions) {
                $listItems = \implode(\PHP_EOL, \array_map(static function (Matrix\Domain\UserId $matrixUserId): string {
                    return <<<HTML
<li>
    {$matrixUserId->toString()}
</li>
HTML;
                }, $matrixUserIdSuggestions));

                $message = get_string(
                    Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_INFO_SUGGESTION,
                    Moodle\Application\Plugin::NAME,
                );

                echo $this->renderer->notification(
                    <<<HTML
<p>
    {$message}
</p>
<ul>
    {$listItems}
</ul>
HTML,
                    output\notification::NOTIFY_INFO,
                );
            }

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

    /**
     * @return array<int, Matrix\Domain\UserId>
     */
    private function matrixUserIdSuggestions(\stdClass $user): array
    {
        if (!\property_exists($user, 'username')) {
            return [];
        }

        $username = $user->username;

        if (!\is_string($username)) {
            return [];
        }

        $values = \array_map(static function (string $homeServer) use ($username): string {
            return \sprintf(
                '@%s:%s',
                $username,
                $homeServer,
            );
        }, $this->homeServers());

        return \array_reduce(
            $values,
            static function (array $matrixUserIds, string $value): array {
                try {
                    $matrixUserId = Matrix\Domain\UserId::fromString($value);
                } catch (\InvalidArgumentException $exception) {
                    return $matrixUserIds;
                }

                $matrixUserIds[] = $matrixUserId;

                return $matrixUserIds;
            },
            [],
        );
    }

    /**
     * @return array<int, string>
     */
    private function homeServers(): array
    {
        $homeServers = [
            'matrix.org',
        ];

        if ('' === $this->moodleConfiguration->homeserverUrl()) {
            return $homeServers;
        }

        $host = \parse_url(
            $this->moodleConfiguration->homeserverUrl(),
            \PHP_URL_HOST,
        );

        if (!\is_string($host)) {
            return $homeServers;
        }

        $parts = \explode(
            '.',
            $host,
        );

        while (\count($parts) > 1) {
            $homeServers[] = \implode(
                '.',
                $parts,
            );

            \array_shift($parts);
        }

        \sort($homeServers);

        return $homeServers;
    }
}
