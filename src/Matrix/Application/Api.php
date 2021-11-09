<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Application;

use mod_matrix\Matrix;

/**
 * @see https://matrix.org/docs/api/client-server/#overview
 */
interface Api
{
    /**
     * @see https://matrix.org/docs/api/client-server/#get-/_matrix/client/r0/account/whoami
     */
    public function whoami(): Matrix\Domain\UserId;

    /**
     * @see https://matrix.org/docs/api/client-server/#post-/_matrix/client/r0/createRoom
     */
    public function createRoom(array $options): Matrix\Domain\RoomId;

    /**
     * @see https://matrix.org/docs/api/client-server/#post-/_matrix/client/r0/rooms/-roomId-/invite
     */
    public function inviteUser(
        Matrix\Domain\UserId $userId,
        Matrix\Domain\RoomId $roomId
    ): void;

    /**
     * @see https://matrix.org/docs/api/client-server/#post-/_matrix/client/r0/rooms/-roomId-/kick
     */
    public function kickUser(
        Matrix\Domain\UserId $userId,
        Matrix\Domain\RoomId $roomId
    ): void;

    /**
     * @see https://matrix.org/docs/api/client-server/#get-/_matrix/client/r0/rooms/-roomId-/state/-eventType-/-stateKey-
     */
    public function getState(
        Matrix\Domain\RoomId $roomId,
        string $eventType,
        string $stateKey
    );

    /**
     * @see https://matrix.org/docs/api/client-server/#put-/_matrix/client/r0/rooms/-roomId-/state/-eventType-/-stateKey-
     *
     * @param mixed $eventType
     * @param mixed $stateKey
     * @param mixed $content
     */
    public function setState(
        Matrix\Domain\RoomId $roomId,
        $eventType,
        $stateKey,
        $content
    ): void;

    /**
     * @see https://matrix.org/docs/api/client-server/#get-/_matrix/client/r0/rooms/-roomId-/members
     *
     * @return array<int, Matrix\Domain\UserId>
     */
    public function getMembersOfRoom(Matrix\Domain\RoomId $roomId): array;

    public function debug($val): void;
}
