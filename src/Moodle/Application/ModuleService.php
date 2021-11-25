<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Application;

use Ergebnis\Clock;
use mod_matrix\Moodle;

final class ModuleService
{
    private $moduleRepository;
    private $clock;

    public function __construct(
        Moodle\Domain\ModuleRepository $moduleRepository,
        Clock\Clock $clock
    ) {
        $this->moduleRepository = $moduleRepository;
        $this->clock = $clock;
    }

    public function create(
        Moodle\Domain\Name $name,
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\SectionId $sectionId
    ): Moodle\Domain\Module {
        $module = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::unknown(),
            Moodle\Domain\Type::fromInt(0),
            $name,
            $courseId,
            $sectionId,
            Moodle\Domain\Timestamp::fromInt($this->clock->now()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt(0),
        );

        $this->moduleRepository->save($module);

        return $module;
    }
}
