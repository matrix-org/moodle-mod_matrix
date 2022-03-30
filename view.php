<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Container;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

require '../../config.php';

require_once __DIR__ . '/vendor/autoload.php';

/** @var int $courseModuleId */
$courseModuleId = required_param(
    'id',
    PARAM_INT,
);

/** @var cm_info $cm */
[$course, $cm] = get_course_and_cm_from_cmid(
    $courseModuleId,
    Plugin\Application\Plugin::NAME,
);

$moduleId = Plugin\Domain\ModuleId::fromString((string) $cm->instance);

$container = Container::instance();

$module = $container->moduleRepository()->findOneBy([
    'id' => $moduleId->toInt(),
]);

if (!$module instanceof Plugin\Domain\Module) {
    throw new \RuntimeException(\sprintf(
        'A Matrix module with id "%d" could not be found.',
        $moduleId->toInt(),
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
    /** @var core_renderer $OUTPUT */
    echo $OUTPUT->header();

    echo $this->renderer->confirm(
        \sprintf(
            '<p>%s</p>%s',
            get_string(
                isguestuser() ? 'view_noguests' : 'view_nojoin',
                Plugin\Application\Plugin::NAME,
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

$frontController = new Plugin\Infrastructure\FrontController(
    $container->roomRepository(),
    $container->moodleGroupRepository(),
    $container->matrixUserIdLoader(),
    $container->roomService(),
    $container->nameService(),
    $container->configuration(),
    $PAGE,
    $OUTPUT,
);

/** @var stdClass $USER */
$frontController->handle(
    $module,
    $cm,
    $USER,
);
