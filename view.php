<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\matrix;

require '../../config.php';

$id = required_param('id', PARAM_INT);
[$course, $cm] = get_course_and_cm_from_cmid($id, 'matrix');

/** @var moodle_database $DB */
$matrix = $DB->get_record('matrix', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

/** @var moodle_page $PAGE */
$PAGE->set_url('/mod/matrix/view.php', ['id' => $cm->id]);
$PAGE->set_title($matrix->name);
$PAGE->set_cacheable(false);
$PAGE->set_heading($course->fullname);

/** @var bootstrap_renderer $OUTPUT */
echo $OUTPUT->header();

if (!has_capability('mod/matrix:view', $PAGE->context)) {
    echo $OUTPUT->confirm(
        sprintf(
            '<p>%s</p>%s',
            get_string(isguestuser() ? 'view_noguests' : 'view_nojoin', 'matrix'),
            get_string('liketologin')
        ),
        get_login_url(),
        new moodle_url('/course/view.php', ['id' => $course->id])
    );
    echo $OUTPUT->footer();

    exit;
}

function matrix_alert(string $type, string $content): string
{
    $types = [
        'danger',
        'dark',
        'info',
        'light',
        'primary',
        'secondary',
        'success',
        'warning',
    ];

    if (!in_array($type, $types, true)) {
        throw new \InvalidArgumentException(sprintf(
            'Type needs to be one "%s", got "%s" instead.',
            implode('", "', $types),
            $type
        ));
    }

    return <<<TXT
<div class="alert alert-${type}">
    ${content}
</div>
TXT;
}

$possible_rooms = $DB->get_records('matrix_rooms', ['course_id' => $matrix->course]);

if (count($possible_rooms) === 0) {
    echo matrix_alert(
        'danger',
        get_string('vw_error_no_rooms', 'matrix')
    );

    echo $OUTPUT->footer();

    exit;
}

$groups = groups_get_all_groups($matrix->course, 0, 0, 'g.*', true);

if (count($groups) === 0) {
    echo matrix_alert(
        'danger',
        get_string('vw_error_no_groups', 'matrix')
    );
    echo $OUTPUT->footer();

    exit;
}

$visible_groups = groups_get_activity_allowed_groups($cm);

if (count($visible_groups) === 0) {
    echo matrix_alert(
        'danger',
        get_string('vw_error_no_visible_groups', 'matrix')
    );

    echo $OUTPUT->footer();

    exit;
}

if (count($visible_groups) === 1) {
    $group = reset($visible_groups);
    $room = $DB->get_record('matrix_rooms', ['course_id' => $matrix->course, 'group_id' => $group->id]);

    if (!$room) {
        echo matrix_alert(
            'danger',
            get_string('vw_error_no_room_in_group', 'matrix')
        );

        echo $OUTPUT->footer();

        exit;
    }
    $room_url = json_encode(matrix::make_room_url($room->room_id));
    echo '<script type="text/javascript">window.location = ' . $room_url . ';</script>';
    echo '<a href="' . $room_url . '">' . get_string('vw_join_btn', 'matrix') . '</a>';
    echo $OUTPUT->footer();

    exit;
}

// else multiple groups are possible

// ... unless there's only one possible option anyways
if (count($possible_rooms) === 1) {
    $room_url = json_encode(matrix::make_room_url(reset($possible_rooms)->room_id));
    echo '<script type="text/javascript">window.location = ' . $room_url . ';</script>';
    echo '<a href="' . $room_url . '">' . get_string('vw_join_btn', 'matrix') . '</a>';
    echo $OUTPUT->footer();

    exit;
}

echo matrix_alert(
    'warning',
    get_string('vw_alert_many_rooms', 'matrix')
);

foreach ($visible_groups as $id => $group) {
    $room = $DB->get_record('matrix_rooms', ['course_id' => $matrix->course, 'group_id' => $group->id]);

    if (!$room) {
        continue;
    }

    $name = groups_get_group_name($group->id);
    $room_url = json_encode(matrix::make_room_url($room->room_id));
    echo '<p><a href="' . $room_url . '">' . $name . '</a></p>';
}

echo $OUTPUT->footer();
