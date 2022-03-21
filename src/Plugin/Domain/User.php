<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Domain;

use mod_matrix\Matrix;

/**
 * @psalm-immutable
 */
final class User
{
    private $id;
    private $matrixUserId;

    private function __construct(
        UserId $id,
        Matrix\Domain\UserId $matrixUserId
    ) {
        $this->id = $id;
        $this->matrixUserId = $matrixUserId;
    }

    public static function create(
        UserId $id,
        Matrix\Domain\UserId $matrixUserId
    ): self {
        return new self(
            $id,
            $matrixUserId,
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function matrixUserId(): Matrix\Domain\UserId
    {
        return $this->matrixUserId;
    }
}
