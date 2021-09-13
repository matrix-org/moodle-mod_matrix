<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Application;

use mod_matrix\Moodle;

interface Api
{
    /**
     * @see https://matrix.org/docs/api/client-server/#!/User32data/getTokenOwner
     */
    public function whoami(): Moodle\Domain\MatrixUserId;

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32creation/createRoom
     * @param mixed $opts
     */
    public function createRoom($opts = []): Moodle\Domain\MatrixRoomId;

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32membership/inviteBy3PID
     */
    public function inviteUser(
        Moodle\Domain\MatrixUserId $userId,
        Moodle\Domain\MatrixRoomId $roomId
    );

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32membership/kick
     */
    public function kickUser(
        Moodle\Domain\MatrixUserId $userId,
        Moodle\Domain\MatrixRoomId $roomId
    );

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32participation/getRoomStateWithKey
     * @param mixed $eventType
     * @param mixed $stateKey
     */
    public function getState(Moodle\Domain\MatrixRoomId $roomId, $eventType, $stateKey);

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32participation/setRoomStateWithKey
     * @param mixed $eventType
     * @param mixed $stateKey
     * @param mixed $content
     */
    public function setState(Moodle\Domain\MatrixRoomId $roomId, $eventType, $stateKey, $content);

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32participation/getMembersByRoom
     *
     * @return array<int, Moodle\Domain\MatrixUserId>
     */
    public function getMembersOfRoom(Moodle\Domain\MatrixRoomId $roomId): array;

    public function debug($val): void;
}
