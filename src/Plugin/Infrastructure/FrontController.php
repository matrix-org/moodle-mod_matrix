<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Container;
use mod_matrix\Matrix;
use mod_matrix\Plugin;

final class FrontController
{
    private $container;
    private $page;
    private $renderer;

    public function __construct(
        Container $container,
        \moodle_page $page,
        \core_renderer $renderer
    ) {
        $this->container = $container;
        $this->page = $page;
        $this->renderer = $renderer;
    }

    public function handle(
        Plugin\Domain\Module $module,
        \cm_info $cm,
        \stdClass $user
    ): void {
        $matrixUserId = $this->container->matrixUserIdLoader()->load($user);

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
            $this->container->configuration(),
        );
    }

    private function listRoomsAction(): Plugin\Infrastructure\Action\ListRoomsAction
    {
        return new Plugin\Infrastructure\Action\ListRoomsAction(
            $this->container->roomRepository(),
            $this->container->moodleGroupRepository(),
            $this->container->matrixUserIdLoader(),
            $this->container->roomService(),
            $this->container->nameService(),
            $this->renderer,
        );
    }
}
