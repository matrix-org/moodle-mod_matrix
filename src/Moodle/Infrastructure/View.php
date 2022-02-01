<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use core\output;
use mod_matrix\Matrix;
use mod_matrix\Moodle;

final class View
{
    private $moodleRoomRepository;
    private $moodleGroupRepository;
    private $moodleMatrixUserIdLoader;
    private $moodleRoomService;
    private $moodleNameService;
    private $page;
    private $renderer;

    public function __construct(
        Moodle\Domain\RoomRepository $moodleRoomRepository,
        Moodle\Domain\GroupRepository $moodleGroupRepository,
        Moodle\Domain\MatrixUserIdLoader $moodleMatrixUserIdLoader,
        Moodle\Application\RoomService $moodleRoomService,
        Moodle\Application\NameService $moodleNameService,
        \moodle_page $page,
        \core_renderer $renderer
    ) {
        $this->moodleRoomRepository = $moodleRoomRepository;
        $this->moodleGroupRepository = $moodleGroupRepository;
        $this->moodleMatrixUserIdLoader = $moodleMatrixUserIdLoader;
        $this->moodleRoomService = $moodleRoomService;
        $this->moodleNameService = $moodleNameService;
        $this->page = $page;
        $this->renderer = $renderer;
    }

    public function render(
        Moodle\Domain\Module $module,
        \cm_info $cm,
        \stdClass $user
    ): void {
        $matrixUserId = $this->moodleMatrixUserIdLoader->load($user);

        if (!$matrixUserId instanceof Matrix\Domain\UserId) {
            $this->editMatrixUserIdFormAction()->handle($user);

            return;
        }

        $isStaff = self::isStaffUserInCourseContext(
            $user,
            $module->courseId(),
        );

        $rooms = $this->moodleRoomRepository->findAllBy([
            'module_id' => $module->id()->toInt(),
        ]);

        if (!$isStaff) {
            $groupsVisibleToUser = groups_get_activity_allowed_groups(
                $cm,
                $user,
            );

            $rooms = \array_filter($rooms, static function (Moodle\Domain\Room $room) use ($groupsVisibleToUser): bool {
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

        if ([] === $rooms) {
            echo $this->renderer->heading(get_string(
                Moodle\Infrastructure\Internationalization::VIEW_HEADER,
                Moodle\Application\Plugin::NAME,
            ));

            echo $this->renderer->notification(
                get_string(
                    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOMS,
                    Moodle\Application\Plugin::NAME,
                ),
                output\notification::NOTIFY_WARNING,
            );

            echo $this->renderer->footer();

            return;
        }

        $courseShortName = Moodle\Domain\CourseShortName::fromString($cm->get_course()->shortname);

        $roomLinks = \array_map(function (Moodle\Domain\Room $room) use ($courseShortName, $module): RoomLink {
            $url = $this->moodleRoomService->urlForRoom($room);

            $groupId = $room->groupId();

            if (!$groupId instanceof Moodle\Domain\GroupId) {
                return RoomLink::create(
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

            return RoomLink::create(
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
                Moodle\Infrastructure\Internationalization::VIEW_HEADER,
                Moodle\Application\Plugin::NAME,
            ));

            echo <<<HTML
<script type="text/javascript">
    window.location.href = '{$roomLink->url()}';
</script>
HTML;

            echo $this->renderer->footer();
        }

        \usort($roomLinks, static function (RoomLink $a, RoomLink $b): int {
            return \strcmp(
                $a->roomName()->toString(),
                $b->roomName()->toString(),
            );
        });

        $listItems = \implode(\PHP_EOL, \array_map(static function (RoomLink $link): string {
            return <<<HTML
<li>
    <a href="{$link->url()}" target="_blank" title="{$link->roomName()->toString()}">{$link->roomName()->toString()}</a>
</li>
HTML;
        }, $roomLinks));

        echo $this->renderer->heading(get_string(
            Moodle\Infrastructure\Internationalization::VIEW_HEADER,
            Moodle\Application\Plugin::NAME,
        ));

        echo <<<HTML
<ul>
    {$listItems}
</ul>
HTML;

        echo $this->renderer->footer();
    }

    private function editMatrixUserIdFormAction(): Moodle\Infrastructure\Action\EditMatrixUserIdAction
    {
        return new Moodle\Infrastructure\Action\EditMatrixUserIdAction(
            $this->page,
            $this->renderer,
        );
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
}
