<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
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
        foreach (self::validUsernames() as $keyUserName => $username) {
            $key = \sprintf(
                'user-id-with-valid-%s-without-home-server',
                $keyUserName,
            );

            yield $key => [
                $username,
            ];
        }

        foreach (self::validUsernames() as $keyUsername => $username) {
            foreach (self::invalidHomeServers() as $keyHomeServer => $homeServer) {
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

        foreach (self::validHomeServers() as $keyHomeServer => $homeServer) {
            $key = \sprintf(
                'user-id-without-username-with-valid-%s',
                $keyHomeServer,
            );

            yield $key => [
                $homeServer,
            ];
        }

        foreach (self::invalidUsernames() as $keyUsername => $username) {
            foreach (self::invalidHomeServers() as $keyHomeServer => $homeServer) {
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

        foreach (self::invalidUsernames() as $keyUsername => $username) {
            foreach (self::validHomeServers() as $keyHomeServer => $homeServer) {
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
        foreach (self::validUsernames() as $keyUsername => $username) {
            foreach (self::validHomeServers() as $keyHomeServer => $homeServer) {
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

    /**
     * @return array<string, string>
     */
    private static function invalidUsernames(): array
    {
        return [
            'username-with-at' => '@foo',
            'username-with-dot' => 'foo.bar',
            'username-with-umlaut' => 'foöbär',
            'username-with-slash-backward' => 'foo\bar',
            'username-with-slash-forward' => 'foo/bar',
            'username-with-space-leading' => ' foo',
            'username-with-space-middle' => 'foo bar',
            'username-with-space-trailing' => 'foo ',
            'username-with-upper-case-letters' => 'foO',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function validUsernames(): array
    {
        return [
            'username-with-digits-only' => '123',
            'username-with-letters-and-digits' => 'foo123',
            'username-with-letters-digits-and-dashes' => 'foo-123',
            'username-with-letters-digits-and-underscores' => 'foo_123',
            'username-with-letters-only' => 'foo',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function invalidHomeServers(): array
    {
        return [
            'home-server-without-tld' => 'example',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function validHomeServers(): array
    {
        return [
            'home-server-with-tld-with-2-letters' => 'example.de',
            'home-server-with-tld-with-2-segments' => 'example.co.uk',
            'home-server-with-tld-with-3-letters' => 'example.org',
        ];
    }
}
