<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Matrix;

/**
 * @psalm-immutable
 */
final class RoomLink
{
    private $url;
    private $roomName;

    private function __construct(
        string $url,
        Matrix\Domain\RoomName $roomName
    ) {
        $this->url = $url;
        $this->roomName = $roomName;
    }

    public static function create(
        string $url,
        Matrix\Domain\RoomName $roomName
    ): self {
        return new self(
            $url,
            $roomName,
        );
    }

    public function url(): string
    {
        return $this->url;
    }

    public function roomName(): Matrix\Domain\RoomName
    {
        return $this->roomName;
    }
}
