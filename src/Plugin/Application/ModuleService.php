<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Application;

use Ergebnis\Clock;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class ModuleService
{
    private $moduleRepository;
    private $clock;

    public function __construct(
        Plugin\Domain\ModuleRepository $moduleRepository,
        Clock\Clock $clock
    ) {
        $this->moduleRepository = $moduleRepository;
        $this->clock = $clock;
    }

    public function create(
        Plugin\Domain\ModuleName $name,
        Plugin\Domain\ModuleTopic $topic,
        Plugin\Domain\ModuleTarget $target,
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\SectionId $sectionId
    ): Plugin\Domain\Module {
        $module = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::unknown(),
            Plugin\Domain\ModuleType::fromInt(0),
            $name,
            $topic,
            $target,
            $courseId,
            $sectionId,
            Moodle\Domain\Timestamp::fromInt($this->clock->now()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt(0),
        );

        $this->moduleRepository->save($module);

        return $module;
    }
}
