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

final class ListRoomsAction
{
    private $moodleRoomRepository;
    private $moodleGroupRepository;
    private $moodleMatrixUserIdLoader;
    private $moodleRoomService;
    private $moodleNameService;
    private $renderer;

    public function __construct(
        Moodle\Domain\RoomRepository $moodleRoomRepository,
        Moodle\Domain\GroupRepository $moodleGroupRepository,
        Moodle\Domain\MatrixUserIdLoader $moodleMatrixUserIdLoader,
        Moodle\Application\RoomService $moodleRoomService,
        Moodle\Application\NameService $moodleNameService,
        \core_renderer $renderer
    ) {
        $this->moodleRoomRepository = $moodleRoomRepository;
        $this->moodleGroupRepository = $moodleGroupRepository;
        $this->moodleMatrixUserIdLoader = $moodleMatrixUserIdLoader;
        $this->moodleRoomService = $moodleRoomService;
        $this->moodleNameService = $moodleNameService;
        $this->renderer = $renderer;
    }

    public function handle(
        \stdClass $user,
        Moodle\Domain\Module $module,
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
                Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER,
                Moodle\Application\Plugin::NAME,
            ));

            echo $this->renderer->notification(
                get_string(
                    Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_WARNING_NO_ROOMS,
                    Moodle\Application\Plugin::NAME,
                ),
                output\notification::NOTIFY_WARNING,
            );

            echo $this->renderer->footer();

            return;
        }

        $matrixUserId = $this->moodleMatrixUserIdLoader->load($user);
        $courseShortName = Moodle\Domain\CourseShortName::fromString($cm->get_course()->shortname);

        $roomLinks = \array_map(function (Moodle\Domain\Room $room) use ($courseShortName, $module, $matrixUserId): Moodle\Infrastructure\RoomLink {
            $url = $this->moodleRoomService->urlForRoom(
                $room,
                $matrixUserId,
            );

            $groupId = $room->groupId();

            if (!$groupId instanceof Moodle\Domain\GroupId) {
                return Moodle\Infrastructure\RoomLink::create(
                    $url,
                    $this->moodleNameService->forCourseAndModule(
                        $courseShortName,
                        $module->name(),
                    ),
                );
            }

            $group = $this->moodleGroupRepository->find($groupId);

            if (!$group instanceof Moodle\Domain\Group) {
                throw Moodle\Domain\GroupNotFound::for($groupId);
            }

            return Moodle\Infrastructure\RoomLink::create(
                $url,
                $this->moodleNameService->forGroupCourseAndModule(
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
                Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER,
                Moodle\Application\Plugin::NAME,
            ));

            echo <<<HTML
<script type="text/javascript">
    window.location.href = '{$roomLink->url()}';
</script>
HTML;

            echo $this->renderer->footer();
        }

        \usort($roomLinks, static function (Moodle\Infrastructure\RoomLink $a, Moodle\Infrastructure\RoomLink $b): int {
            return \strcmp(
                $a->roomName()->toString(),
                $b->roomName()->toString(),
            );
        });

        $listItems = \implode(\PHP_EOL, \array_map(static function (Moodle\Infrastructure\RoomLink $link): string {
            return <<<HTML
<li>
    <a href="{$link->url()}" target="_blank" title="{$link->roomName()->toString()}">{$link->roomName()->toString()}</a>
</li>
HTML;
        }, $roomLinks));

        echo $this->renderer->heading(get_string(
            Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER,
            Moodle\Application\Plugin::NAME,
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
     * @return array<int, Moodle\Domain\Room>
     */
    private function rooms(
        Moodle\Domain\Module $module,
        bool $isStaff,
        \cm_info $cm,
        \stdClass $user
    ): array {
        $rooms = $this->moodleRoomRepository->findAllBy([
            'module_id' => $module->id()->toInt(),
        ]);

        if ($isStaff) {
            return $rooms;
        }

        $groupsVisibleToUser = groups_get_activity_allowed_groups(
            $cm,
            $user,
        );

        return \array_filter($rooms, static function (Moodle\Domain\Room $room) use ($groupsVisibleToUser): bool {
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
