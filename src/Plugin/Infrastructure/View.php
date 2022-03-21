<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class View
{
    private $roomRepository;
    private $moodleGroupRepository;
    private $matrixUserIdLoader;
    private $roomService;
    private $nameService;
    private $configuration;
    private $page;
    private $renderer;

    public function __construct(
        Plugin\Domain\RoomRepository $roomRepository,
        Moodle\Domain\GroupRepository $moodleGroupRepository,
        Plugin\Domain\MatrixUserIdLoader $matrixUserIdLoader,
        Plugin\Application\RoomService $roomService,
        Plugin\Application\NameService $nameService,
        Plugin\Application\Configuration $configuration,
        \moodle_page $page,
        \core_renderer $renderer
    ) {
        $this->roomRepository = $roomRepository;
        $this->moodleGroupRepository = $moodleGroupRepository;
        $this->matrixUserIdLoader = $matrixUserIdLoader;
        $this->roomService = $roomService;
        $this->nameService = $nameService;
        $this->configuration = $configuration;
        $this->page = $page;
        $this->renderer = $renderer;
    }

    public function render(
        Plugin\Domain\Module $module,
        \cm_info $cm,
        \stdClass $user
    ): void {
        $matrixUserId = $this->matrixUserIdLoader->load($user);

        if (!$matrixUserId instanceof Matrix\Domain\UserId) {
            $this->editMatrixUserIdFormAction()->handle($user);

            return;
        }

        $this->listRoomsAction()->handle(
            $user,
            $module,
            $cm,
        );
    }

    private function editMatrixUserIdFormAction(): Plugin\Infrastructure\Action\EditMatrixUserIdAction
    {
        return new Plugin\Infrastructure\Action\EditMatrixUserIdAction(
            $this->page,
            $this->renderer,
            $this->configuration,
        );
    }

    private function listRoomsAction(): Plugin\Infrastructure\Action\ListRoomsAction
    {
        return new Plugin\Infrastructure\Action\ListRoomsAction(
            $this->roomRepository,
            $this->moodleGroupRepository,
            $this->matrixUserIdLoader,
            $this->roomService,
            $this->nameService,
            $this->renderer,
        );
    }
}
