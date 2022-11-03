<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class ModuleNormalizer
{
    public function denormalize(object $normalized): Plugin\Domain\Module
    {
        return Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromString((string) $normalized->id),
            Plugin\Domain\ModuleType::fromString((string) $normalized->type),
            Plugin\Domain\ModuleName::fromString((string) $normalized->name),
            Plugin\Domain\ModuleTopic::fromString((string) $normalized->topic),
            Plugin\Domain\ModuleTarget::fromString((string) $normalized->target),
            Moodle\Domain\CourseId::fromString((string) $normalized->course),
            Moodle\Domain\SectionId::fromString((string) $normalized->section),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timecreated),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timemodified),
        );
    }

    public function normalize(Plugin\Domain\Module $denormalized): object
    {
        return (object) [
            'id' => $denormalized->id()->toInt(),
            'type' => $denormalized->type()->toInt(),
            'name' => $denormalized->name()->toString(),
            'topic' => $denormalized->topic()->toString(),
            'target' => $denormalized->target()->toString(),
            'course' => $denormalized->courseId()->toInt(),
            'section' => $denormalized->sectionId()->toInt(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
        ];
    }
}
