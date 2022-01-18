<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Container;
use mod_matrix\Moodle;

require '../../config.php';

require_once __DIR__ . '/vendor/autoload.php';

$courseModuleId = required_param(
    'id',
    PARAM_INT,
);

[$course, $cm] = get_course_and_cm_from_cmid(
    $courseModuleId,
    Moodle\Application\Plugin::NAME,
);

$container = Container::instance();

$module = $container->moodleModuleRepository()->findOneBy([
    'id' => $cm->instance,
]);

if (!$module instanceof Moodle\Domain\Module) {
    throw new \RuntimeException(\sprintf(
        'A Matrix module with id "%s" could not be found.',
        $cm->instance,
    ));
}

require_login(
    $course,
    true,
    $cm,
);

/** @var moodle_page $PAGE */
$PAGE->set_cacheable(false);
$PAGE->set_heading($course->fullname);
$PAGE->set_title($module->name()->toString());
$PAGE->set_url('/mod/matrix/view.php', [
    'id' => $cm->id,
]);

/** @var core_renderer $OUTPUT */
echo $OUTPUT->header();

if (!has_capability('mod/matrix:view', $PAGE->context)) {
    echo $this->renderer->confirm(
        \sprintf(
            '<p>%s</p>%s',
            get_string(
                isguestuser() ? 'view_noguests' : 'view_nojoin',
                Moodle\Application\Plugin::NAME,
            ),
            get_string('liketologin'),
        ),
        get_login_url(),
        new moodle_url('/course/view.php', [
            'id' => $module->courseId()->toInt(),
        ]),
    );

    echo $OUTPUT->footer();

    exit;
}

$view = new Moodle\Infrastructure\View(
    $container->moodleRoomRepository(),
    $container->moodleRoomService(),
);

$view->render(
    $module,
    $cm,
);

echo $OUTPUT->footer();
