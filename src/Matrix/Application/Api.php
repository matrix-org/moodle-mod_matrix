<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Application;

interface Api
{
    /**
     * @see https://matrix.org/docs/api/client-server/#!/User32data/getTokenOwner
     */
    public function whoami();

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32creation/createRoom
     * @param mixed $opts
     */
    public function createRoom($opts = []);

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32membership/inviteBy3PID
     * @param mixed $userId
     * @param mixed $roomId
     */
    public function inviteUser($userId, $roomId);

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32membership/kick
     * @param mixed $userId
     * @param mixed $roomId
     */
    public function kickUser($userId, $roomId);

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32participation/getRoomStateWithKey
     * @param mixed $roomId
     * @param mixed $eventType
     * @param mixed $stateKey
     */
    public function getState($roomId, $eventType, $stateKey);

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32participation/setRoomStateWithKey
     * @param mixed $roomId
     * @param mixed $eventType
     * @param mixed $stateKey
     * @param mixed $content
     */
    public function setState($roomId, $eventType, $stateKey, $content);

    /**
     * @see https://matrix.org/docs/api/client-server/#!/Room32participation/getMembersByRoom
     * @param mixed $roomId
     */
    public function getEffectiveJoins($roomId);

    public function debug($val): void;
}
