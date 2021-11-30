<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Container;
use mod_matrix\Moodle;
use mod_matrix\Twitter;

require '../../config.php';

require_once __DIR__ . '/vendor/autoload.php';

$id = required_param('id', PARAM_INT);

[$course, $cm] = get_course_and_cm_from_cmid(
    $id,
    Moodle\Application\Plugin::NAME,
);

$container = Container::instance();

$moduleRepository = $container->moduleRepository();

$module = $moduleRepository->findOneBy([
    'id' => $cm->instance,
]);

if (!$module instanceof Moodle\Domain\Module) {
    throw new \RuntimeException(\sprintf(
        'A Matrix module with id "%s" could not be found.',
        $cm->instance,
    ));
}

require_login($course, true, $cm);

/** @var moodle_page $PAGE */
$PAGE->set_url('/mod/matrix/view.php', ['id' => $cm->id]);
$PAGE->set_title($module->name()->toString());
$PAGE->set_cacheable(false);
$PAGE->set_heading($course->fullname);

/** @var bootstrap_renderer $OUTPUT */
echo $OUTPUT->header();

if (!has_capability('mod/matrix:view', $PAGE->context)) {
    echo $OUTPUT->confirm(
        \sprintf(
            '<p>%s</p>%s',
            get_string(
                isguestuser() ? 'view_noguests' : 'view_nojoin',
                Moodle\Application\Plugin::NAME,
            ),
            get_string('liketologin'),
        ),
        get_login_url(),
        new moodle_url('/course/view.php', ['id' => $course->id]),
    );

    echo $OUTPUT->footer();

    exit;
}

$roomRepository = $container->roomRepository();

$possibleRooms = $roomRepository->findAllBy([
    'module_id' => $module->id()->toInt(),
]);

if ([] === $possibleRooms) {
    echo Twitter\Bootstrap::alert(
        'danger',
        get_string(
            Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOMS,
            Moodle\Application\Plugin::NAME,
        ),
    );

    echo $OUTPUT->footer();

    exit;
}

$roomService = $container->roomService();

if (\count($possibleRooms) === 1) {
    $firstPossibleRoom = \reset($possibleRooms);

    $roomUrl = \json_encode($roomService->urlForRoom($firstPossibleRoom->matrixRoomId()));

    echo '<script type="text/javascript">window.location = ' . $roomUrl . ';</script>';
    echo '<a href="' . $roomUrl . '">' . get_string(Moodle\Infrastructure\Internationalization::VIEW_BUTTON_JOIN_ROOM, Moodle\Application\Plugin::NAME) . '</a>';

    echo $OUTPUT->footer();

    exit;
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

    echo $OUTPUT->footer();

    exit;
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

    echo $OUTPUT->footer();

    exit;
}

if (\count($visibleGroups) === 1) {
    $group = \reset($visibleGroups);

    $room = $roomRepository->findOneBy([
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

        echo $OUTPUT->footer();

        exit;
    }

    $roomUrl = \json_encode($roomService->urlForRoom($room->matrixRoomId()));

    echo '<script type="text/javascript">window.location = ' . $roomUrl . ';</script>';
    echo '<a href="' . $roomUrl . '">' . get_string(Moodle\Infrastructure\Internationalization::VIEW_BUTTON_JOIN_ROOM, Moodle\Application\Plugin::NAME) . '</a>';

    echo $OUTPUT->footer();

    exit;
}

// else multiple groups are possible

echo Twitter\Bootstrap::alert(
    'warning',
    get_string(
        Moodle\Infrastructure\Internationalization::VIEW_ALERT_MANY_ROOMS,
        Moodle\Application\Plugin::NAME,
    ),
);

foreach ($visibleGroups as $id => $group) {
    $room = $roomRepository->findOneBy([
        'group_id' => $group->id,
        'module_id' => $module->id()->toInt(),
    ]);

    if (!$room instanceof Moodle\Domain\Room) {
        continue;
    }

    $name = groups_get_group_name($group->id);

    $roomUrl = \json_encode($roomService->urlForRoom($room->matrixRoomId()));

    echo '<p><a href="' . $roomUrl . '">' . $name . '</a></p>';
}

echo $OUTPUT->footer();
