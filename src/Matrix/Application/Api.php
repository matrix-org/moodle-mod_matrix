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
 * @see https://spec.matrix.org/latest/client-server-api/
 */
interface Api
{
    /**
     * @see https://matrix.org/docs/api/client-server/#get-/_matrix/client/v3/account/whoami
     * @see https://spec.matrix.org/latest/client-server-api/#get_matrixclientv3accountwhoami
     */
    public function whoAmI(): Matrix\Domain\UserId;

    /**
     * @see https://matrix.org/docs/api/client-server/#post-/_matrix/client/v3/createRoom
     * @see https://spec.matrix.org/latest/client-server-api/#post_matrixclientv3createroom
     */
    public function createRoom(array $options): Matrix\Domain\RoomId;

    /**
     * @see https://matrix.org/docs/api/client-server/#post-/_matrix/client/v3/rooms/-roomId-/invite
     * @see https://spec.matrix.org/latest/client-server-api/#post_matrixclientv3roomsroomidinvite
     */
    public function inviteUser(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\UserId $userId
    ): void;

    /**
     * @see https://matrix.org/docs/api/client-server/#post-/_matrix/client/v3/rooms/-roomId-/kick
     * @see https://spec.matrix.org/latest/client-server-api/#post_matrixclientv3roomsroomidkick
     */
    public function kickUser(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\UserId $userId
    ): void;

    /**
     * @see https://matrix.org/docs/api/client-server/#get-/_matrix/client/v3/rooms/-roomId-/state/-eventType-/-stateKey-
     * @see https://spec.matrix.org/latest/client-server-api/#get_matrixclientv3roomsroomidstateeventtypestatekey
     */
    public function getState(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\EventType $eventType,
        Matrix\Domain\StateKey $stateKey
    );

    /**
     * @see https://matrix.org/docs/api/client-server/#put-/_matrix/client/v3/rooms/-roomId-/state/-eventType-/-stateKey-
     * @see https://spec.matrix.org/latest/client-server-api/#put_matrixclientv3roomsroomidstateeventtypestatekey
     */
    public function setState(
        Matrix\Domain\RoomId $roomId,
        Matrix\Domain\EventType $eventType,
        Matrix\Domain\StateKey $stateKey,
        array $state
    ): void;

    /**
     * @see https://matrix.org/docs/api/client-server/#get-/_matrix/client/v3/rooms/-roomId-/joined_members
     * @see https://spec.matrix.org/latest/client-server-api/#get_matrixclientv3roomsroomidjoined_members
     */
    public function listUsers(Matrix\Domain\RoomId $roomId): Matrix\Domain\UserIdCollection;
}
