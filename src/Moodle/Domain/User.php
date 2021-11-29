<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Domain;

use mod_matrix\Matrix;

/**
 * @psalm-immutable
 */
final class User
{
    private $matrixUserId;

    private function __construct(Matrix\Domain\UserId $matrixUserId)
    {
        $this->matrixUserId = $matrixUserId;
    }

    public static function create(Matrix\Domain\UserId $matrixUserId): self
    {
        return new self($matrixUserId);
    }

    public function matrixUserId(): Matrix\Domain\UserId
    {
        return $this->matrixUserId;
    }
}
