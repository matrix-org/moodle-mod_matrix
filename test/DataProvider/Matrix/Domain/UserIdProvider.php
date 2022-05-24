<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\DataProvider\Matrix\Domain;

use Ergebnis\DataProvider;

final class UserIdProvider extends DataProvider\AbstractProvider
{
    /**
     * @return \Generator<string, array{0: string}>
     */
    public function invalid(): \Generator
    {
        foreach (UsernameProvider::validValues() as $keyUserName => $username) {
            $key = \sprintf(
                'user-id-with-valid-%s-without-home-server',
                $keyUserName,
            );

            yield $key => [
                $username,
            ];
        }

        foreach (UsernameProvider::validValues() as $keyUsername => $username) {
            foreach (HomeserverProvider::invalidValues() as $keyHomeServer => $homeServer) {
                $key = \sprintf(
                    'user-id-with-valid-%s-with-invalid-%s',
                    $keyUsername,
                    $keyHomeServer,
                );

                $userId = \sprintf(
                    '@%s:%s',
                    $username,
                    $homeServer,
                );

                yield $key => [
                    $userId,
                ];
            }
        }

        foreach (HomeserverProvider::validValues() as $keyHomeServer => $homeServer) {
            $key = \sprintf(
                'user-id-without-username-with-valid-%s',
                $keyHomeServer,
            );

            yield $key => [
                $homeServer,
            ];
        }

        foreach (UsernameProvider::invalidValues() as $keyUsername => $username) {
            foreach (HomeserverProvider::invalidValues() as $keyHomeServer => $homeServer) {
                $key = \sprintf(
                    'user-id-with-invalid-%s-with-invalid-%s',
                    $keyUsername,
                    $keyHomeServer,
                );

                $userId = \sprintf(
                    '@%s:%s',
                    $username,
                    $homeServer,
                );

                yield $key => [
                    $userId,
                ];
            }
        }

        foreach (UsernameProvider::invalidValues() as $keyUsername => $username) {
            foreach (HomeserverProvider::validValues() as $keyHomeServer => $homeServer) {
                $key = \sprintf(
                    'user-id-with-invalid-%s-with-valid-%s',
                    $keyUsername,
                    $keyHomeServer,
                );

                $userId = \sprintf(
                    '@%s:%s',
                    $username,
                    $homeServer,
                );

                yield $key => [
                    $userId,
                ];
            }
        }
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public function valid(): \Generator
    {
        foreach (UsernameProvider::validValues() as $keyUsername => $username) {
            foreach (HomeserverProvider::validValues() as $keyHomeServer => $homeServer) {
                $key = \sprintf(
                    'user-id-%s-with-%s',
                    $keyUsername,
                    $keyHomeServer,
                );

                $userId = \sprintf(
                    '@%s:%s',
                    $username,
                    $homeServer,
                );

                yield $key => [
                    $userId,
                ];
            }
        }
    }
}
