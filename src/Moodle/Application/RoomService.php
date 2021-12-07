<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Application;

use mod_matrix\Moodle;

final class RoomService
{
    private $configuration;
    private $moduleRepository;

    public function __construct(
        Moodle\Application\Configuration $configuration,
        Moodle\Domain\ModuleRepository $moduleRepository
    ) {
        $this->configuration = $configuration;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * @throws Moodle\Domain\ModuleNotFound
     */
    public function urlForRoom(Moodle\Domain\Room $room): string
    {
        if ('' === $this->configuration->elementUrl()) {
            return \sprintf(
                'https://matrix.to/#/%s',
                $room->matrixRoomId()->toString(),
            );
        }

        $module = $this->moduleRepository->findOneBy([
            'id' => $room->moduleId()->toInt(),
        ]);

        if (!$module instanceof Moodle\Domain\Module) {
            throw Moodle\Domain\ModuleNotFound::for($room->moduleId());
        }

        if ($module->target()->equals(Moodle\Domain\ModuleTarget::matrixTo())) {
            return \sprintf(
                'https://matrix.to/#/%s',
                $room->matrixRoomId()->toString(),
            );
        }

        return \sprintf(
            '%s/#/room/%s',
            $this->configuration->elementUrl(),
            $room->matrixRoomId()->toString(),
        );
    }
}
