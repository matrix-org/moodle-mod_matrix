<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Infrastructure;

use mod_matrix\Matrix;

final class ModuleNormalizer
{
    public function denormalize(object $normalized): Matrix\Domain\Module
    {
        return Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::fromString((string) $normalized->id),
            Matrix\Domain\Type::fromString((string) $normalized->type),
            Matrix\Domain\Name::fromString((string) $normalized->name),
            Matrix\Domain\CourseId::fromString((string) $normalized->course),
            Matrix\Domain\Timestamp::fromString((string) $normalized->timecreated),
            Matrix\Domain\Timestamp::fromString((string) $normalized->timemodified)
        );
    }

    public function normalize(Matrix\Domain\Module $denormalized): object
    {
        return (object) [
            'id' => $denormalized->id()->toInt(),
            'type' => $denormalized->type()->toInt(),
            'name' => $denormalized->name()->toString(),
            'course' => $denormalized->courseId()->toInt(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
        ];
    }
}
