<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Infrastructure\Action;

use core\event;
use core\output;
use mod_matrix\Matrix;
use mod_matrix\Plugin;

final class EditMatrixUserIdAction
{
    private $page;
    private $renderer;
    private $configuration;

    public function __construct(
        \moodle_page $page,
        \core_renderer $renderer,
        Plugin\Application\Configuration $configuration
    ) {
        $this->page = $page;
        $this->renderer = $renderer;
        $this->configuration = $configuration;
    }

    public function handle(\stdClass $user): void
    {
        $matrixUserIdSuggestions = $this->matrixUserIdSuggestions($user);

        $matrixUserIdForm = new Plugin\Infrastructure\Form\EditMatrixUserIdForm(
            $this->page->url->out(true),
            [
                'matrixUserIdSuggestions' => $matrixUserIdSuggestions,
            ],
        );

        if (!$matrixUserIdForm->is_submitted()) {
            echo $this->renderer->heading(get_string(
                Plugin\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_HEADER,
                Plugin\Application\Plugin::NAME,
            ));

            echo $this->renderer->notification(
                get_string(
                    Plugin\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_WARNING_NO_MATRIX_USER_ID,
                    Plugin\Application\Plugin::NAME,
                ),
                output\notification::NOTIFY_WARNING,
            );

            if ([] !== $matrixUserIdSuggestions) {
                $listItems = \implode(\PHP_EOL, \array_map(static function (Matrix\Domain\UserId $matrixUserId): string {
                    return <<<HTML
<li>
    {$matrixUserId->toString()}
</li>
HTML;
                }, $matrixUserIdSuggestions));

                $message = get_string(
                    Plugin\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_INFO_SUGGESTION,
                    Plugin\Application\Plugin::NAME,
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
                Plugin\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_HEADER,
                Plugin\Application\Plugin::NAME,
            ));

            $matrixUserIdForm->display();

            echo $this->renderer->footer();

            return;
        }

        $data = $matrixUserIdForm->get_data();

        $name = Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME;

        profile_save_custom_fields($user->id, [
            Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME => $data->{$name},
        ]);

        event\user_updated::create_from_userid($user->id)->trigger();

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

        if ('' === $this->configuration->homeserverUrl()->toString()) {
            return $homeServers;
        }

        $host = \parse_url(
            $this->configuration->homeserverUrl()->toString(),
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
