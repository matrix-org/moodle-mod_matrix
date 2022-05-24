<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Infrastructure\Action;

use core\output;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class ListRoomsAction
{
    private $roomRepository;
    private $moodleGroupRepository;
    private $matrixUserIdLoader;
    private $roomService;
    private $nameService;
    private $renderer;

    public function __construct(
        Plugin\Domain\RoomRepository $roomRepository,
        Moodle\Domain\GroupRepository $moodleGroupRepository,
        Plugin\Domain\MatrixUserIdLoader $matrixUserIdLoader,
        Plugin\Application\RoomService $roomService,
        Plugin\Application\NameService $nameService,
        \core_renderer $renderer
    ) {
        $this->roomRepository = $roomRepository;
        $this->moodleGroupRepository = $moodleGroupRepository;
        $this->matrixUserIdLoader = $matrixUserIdLoader;
        $this->roomService = $roomService;
        $this->nameService = $nameService;
        $this->renderer = $renderer;
    }

    public function handle(
        \stdClass $user,
        Plugin\Domain\Module $module,
        \cm_info $cm
    ): void {
        $isStaff = self::isStaffUserInCourseContext(
            $user,
            $module->courseId(),
        );

        $rooms = $this->rooms(
            $module,
            $isStaff,
            $cm,
            $user,
        );

        if ([] === $rooms) {
            echo $this->renderer->heading(get_string(
                Plugin\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER,
                Plugin\Application\Plugin::NAME,
            ));

            echo $this->renderer->notification(
                get_string(
                    Plugin\Infrastructure\Internationalization::ACTION_LIST_ROOMS_WARNING_NO_ROOMS,
                    Plugin\Application\Plugin::NAME,
                ),
                output\notification::NOTIFY_WARNING,
            );

            echo $this->renderer->footer();

            return;
        }

        $matrixUserId = $this->matrixUserIdLoader->load($user);
        $courseShortName = Moodle\Domain\CourseShortName::fromString($cm->get_course()->shortname);

        $roomLinks = \array_map(function (Plugin\Domain\Room $room) use ($courseShortName, $module, $matrixUserId): Plugin\Domain\RoomLink {
            $url = $this->roomService->urlForRoom(
                $room,
                $matrixUserId,
            );

            $groupId = $room->groupId();

            if (!$groupId instanceof Moodle\Domain\GroupId) {
                return Plugin\Domain\RoomLink::create(
                    $url,
                    $this->nameService->forCourseAndModule(
                        $courseShortName,
                        $module->name(),
                    ),
                );
            }

            $group = $this->moodleGroupRepository->find($groupId);

            if (!$group instanceof Moodle\Domain\Group) {
                throw Moodle\Domain\GroupNotFound::for($groupId);
            }

            return Plugin\Domain\RoomLink::create(
                $url,
                $this->nameService->forGroupCourseAndModule(
                    $group->name(),
                    $courseShortName,
                    $module->name(),
                ),
            );
        }, $rooms);

        if (
            !$isStaff
            && \count($roomLinks) === 1
        ) {
            $roomLink = \reset($roomLinks);

            echo $this->renderer->heading(get_string(
                Plugin\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER,
                Plugin\Application\Plugin::NAME,
            ));

            echo <<<HTML
<script type="text/javascript">
    window.location.href = '{$roomLink->url()->toString()}';
</script>
HTML;

            echo $this->renderer->footer();
        }

        \usort($roomLinks, static function (Plugin\Domain\RoomLink $a, Plugin\Domain\RoomLink $b): int {
            return \strcmp(
                $a->roomName()->toString(),
                $b->roomName()->toString(),
            );
        });

        $listItems = \implode(\PHP_EOL, \array_map(static function (Plugin\Domain\RoomLink $link): string {
            return <<<HTML
<li>
    <a href="{$link->url()->toString()}" target="_blank" title="{$link->roomName()->toString()}">{$link->roomName()->toString()}</a>
</li>
HTML;
        }, $roomLinks));

        echo $this->renderer->heading(get_string(
            Plugin\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER,
            Plugin\Application\Plugin::NAME,
        ));

        echo <<<HTML
<ul>
    {$listItems}
</ul>
HTML;

        echo $this->renderer->footer();
    }

    private static function isStaffUserInCourseContext(
        \stdClass $user,
        Moodle\Domain\CourseId $courseId
    ): bool {
        $context = \context_course::instance($courseId->toInt());

        $staffUsersInCourseContext = get_users_by_capability(
            $context,
            'mod/matrix:staff',
        );

        foreach ($staffUsersInCourseContext as $staffUserInCourseContext) {
            if ($user->id === $staffUserInCourseContext->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, Plugin\Domain\Room>
     */
    private function rooms(
        Plugin\Domain\Module $module,
        bool $isStaff,
        \cm_info $cm,
        \stdClass $user
    ): array {
        $rooms = $this->roomRepository->findAllBy([
            'module_id' => $module->id()->toInt(),
        ]);

        if ($isStaff) {
            return $rooms;
        }

        $groupsVisibleToUser = groups_get_activity_allowed_groups(
            $cm,
            $user,
        );

        return \array_filter($rooms, static function (Plugin\Domain\Room $room) use ($groupsVisibleToUser): bool {
            if (!$room->groupId() instanceof Moodle\Domain\GroupId) {
                return true;
            }

            foreach ($groupsVisibleToUser as $groupVisibleToUser) {
                if ($room->groupId()->equals(Moodle\Domain\GroupId::fromString($groupVisibleToUser->id))) {
                    return true;
                }
            }

            return false;
        });
    }
}
