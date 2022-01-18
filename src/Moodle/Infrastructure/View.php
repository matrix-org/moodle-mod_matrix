<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use mod_matrix\Moodle;
use mod_matrix\Twitter;

final class View
{
    private $moodleRoomRepository;
    private $moodleRoomService;

    public function __construct(
        Moodle\Domain\RoomRepository $moodleRoomRepository,
        Moodle\Application\RoomService $moodleRoomService
    ) {
        $this->moodleRoomRepository = $moodleRoomRepository;
        $this->moodleRoomService = $moodleRoomService;
    }

    public function render(
        Moodle\Domain\Module $module,
        \cm_info $cm
    ): void {
        $rooms = $this->moodleRoomRepository->findAllBy([
            'module_id' => $module->id()->toInt(),
        ]);

        if ([] === $rooms) {
            echo Twitter\Bootstrap::alert(
                'danger',
                get_string(
                    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOMS,
                    Moodle\Application\Plugin::NAME,
                ),
            );

            return;
        }

        if (\count($rooms) === 1) {
            $firstPossibleRoom = \reset($rooms);

            $roomUrl = $this->moodleRoomService->urlForRoom($firstPossibleRoom);

            $title = get_string(
                Moodle\Infrastructure\Internationalization::VIEW_BUTTON_JOIN_ROOM,
                Moodle\Application\Plugin::NAME,
            );

            echo <<<HTML
<script type="text/javascript">window.location = {$roomUrl};</script>
<a href="{$roomUrl}">{$title}</a>';
HTML;

            return;
        }

        $groups = groups_get_all_groups(
            $module->courseId()->toInt(),
            0,
            0,
            'g.*',
            true,
        );

        if (\count($groups) === 0) {
            echo Twitter\Bootstrap::alert(
                'danger',
                get_string(
                    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_GROUPS,
                    Moodle\Application\Plugin::NAME,
                ),
            );

            return;
        }

        $visibleGroups = groups_get_activity_allowed_groups($cm);

        if (\count($visibleGroups) === 0) {
            echo Twitter\Bootstrap::alert(
                'danger',
                get_string(
                    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_VISIBLE_GROUPS,
                    Moodle\Application\Plugin::NAME,
                ),
            );

            return;
        }

        if (\count($visibleGroups) === 1) {
            $group = \reset($visibleGroups);

            $room = $this->moodleRoomRepository->findOneBy([
                'group_id' => $group->id,
                'module_id' => $module->id()->toInt(),
            ]);

            if (!$room instanceof Moodle\Domain\Room) {
                echo Twitter\Bootstrap::alert(
                    'danger',
                    get_string(
                        Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOM_IN_GROUP,
                        Moodle\Application\Plugin::NAME,
                    ),
                );

                return;
            }

            $roomUrl = $this->moodleRoomService->urlForRoom($room);

            $title = get_string(
                Moodle\Infrastructure\Internationalization::VIEW_BUTTON_JOIN_ROOM,
                Moodle\Application\Plugin::NAME,
            );

            echo <<<HTML
<script type="text/javascript">window.location = {$roomUrl};</script>
<a href="{$roomUrl}">{$title}</a>
HTML;

            return;
        }

        echo Twitter\Bootstrap::alert(
            'warning',
            get_string(
                Moodle\Infrastructure\Internationalization::VIEW_ALERT_MANY_ROOMS,
                Moodle\Application\Plugin::NAME,
            ),
        );

        foreach ($visibleGroups as $group) {
            $room = $this->moodleRoomRepository->findOneBy([
                'group_id' => $group->id,
                'module_id' => $module->id()->toInt(),
            ]);

            if (!$room instanceof Moodle\Domain\Room) {
                continue;
            }

            $roomUrl = $this->moodleRoomService->urlForRoom($room);
            $name = groups_get_group_name($group->id);

            echo <<<HTML
<p>
    <a href="{$roomUrl}">{$name}</a>
</p>
HTML;
        }
    }
}
