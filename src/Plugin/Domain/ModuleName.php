<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Domain;

/**
 * @psalm-immutable
 */
final class ModuleName
{
    public const LENGTH_MAX = 255;
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        if (self::LENGTH_MAX < \mb_strlen($value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Value "%s" is longer than %d characters.',
                $value,
                self::LENGTH_MAX,
            ));
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
