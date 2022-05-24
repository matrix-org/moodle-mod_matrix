<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Domain;

use mod_matrix\Matrix;
use mod_matrix\Plugin;

/**
 * @psalm-immutable
 */
final class RoomLink
{
    private $url;
    private $roomName;

    private function __construct(
        Plugin\Domain\Url $url,
        Matrix\Domain\RoomName $roomName
    ) {
        $this->url = $url;
        $this->roomName = $roomName;
    }

    public static function create(
        Plugin\Domain\Url $url,
        Matrix\Domain\RoomName $roomName
    ): self {
        return new self(
            $url,
            $roomName,
        );
    }

    public function url(): Plugin\Domain\Url
    {
        return $this->url;
    }

    public function roomName(): Matrix\Domain\RoomName
    {
        return $this->roomName;
    }
}
