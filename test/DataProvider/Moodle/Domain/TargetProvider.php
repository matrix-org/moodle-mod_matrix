<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\DataProvider\Moodle\Domain;

use Ergebnis\DataProvider;
use mod_matrix\Moodle;

final class TargetProvider extends DataProvider\AbstractProvider
{
    /**
     * @return \Generator<string, array{0: string}>
     */
    public function known(): \Generator
    {
        $values = [
            Moodle\Domain\ModuleTarget::elementUrl()->toString(),
            Moodle\Domain\ModuleTarget::matrixTo()->toString(),
        ];

        return self::provideDataForValues(\array_combine(
            $values,
            $values,
        ));
    }
}
