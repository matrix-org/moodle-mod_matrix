<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Application;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Application\Configuration
 */
final class ConfigurationTest extends Framework\TestCase
{
    use Util\Helper;

    public function testDefaultReturnsConfiguration(): void
    {
        $configuration = Matrix\Application\Configuration::default();

        self::assertSame('', $configuration->accessToken());
        self::assertSame('https://matrix-client.matrix.org', $configuration->elementUrl());
        self::assertSame('', $configuration->hsUrl());
    }

    public function testFromObjectRejectsObjectWhenAccessTokenPropertyIsMissing(): void
    {
        $faker = self::faker();

        $object = new \stdClass();

        $object->element_url = $faker->url();
        $object->hs_url = $faker->url();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Configuration should have an "%s" property, but it does not.',
            'access_token',
        ));

        Matrix\Application\Configuration::fromObject($object);
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\BoolProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\FloatProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\NullProvider::null()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\ObjectProvider::object()
     *
     * @param mixed $accessToken
     */
    public function testFromObjectRejectsObjectWhenAccessTokenPropertyIsNotAString($accessToken): void
    {
        $faker = self::faker();

        $object = new \stdClass();

        $object->access_token = $accessToken;
        $object->element_url = $faker->url();
        $object->hs_url = $faker->url();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Configuration "%s" should be a string, got "%s" instead.',
            'access_token',
            is_object($accessToken) ? get_class($accessToken) : gettype($accessToken),
        ));

        Matrix\Application\Configuration::fromObject($object);
    }

    public function testFromObjectRejectsObjectWhenElementUrlPropertyIsMissing(): void
    {
        $faker = self::faker();

        $object = new \stdClass();

        $object->access_token = $faker->sha1();
        $object->hs_url = $faker->url();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Configuration should have an "%s" property, but it does not.',
            'element_url',
        ));

        Matrix\Application\Configuration::fromObject($object);
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\BoolProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\FloatProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\NullProvider::null()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\ObjectProvider::object()
     *
     * @param mixed $elementUrl
     */
    public function testFromObjectRejectsObjectWhenElementUrlPropertyIsNotAString($elementUrl): void
    {
        $faker = self::faker();

        $object = new \stdClass();

        $object->access_token = $faker->sha1();
        $object->element_url = $elementUrl;
        $object->hs_url = $faker->url();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Configuration "%s" should be a string, got "%s" instead.',
            'element_url',
            is_object($elementUrl) ? get_class($elementUrl) : gettype($elementUrl),
        ));

        Matrix\Application\Configuration::fromObject($object);
    }

    public function testFromObjectRejectsObjectWhenHsUrlPropertyIsMissing(): void
    {
        $faker = self::faker();

        $object = new \stdClass();

        $object->access_token = $faker->sha1();
        $object->element_url = $faker->sha1();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Configuration should have an "%s" property, but it does not.',
            'hs_url',
        ));

        Matrix\Application\Configuration::fromObject($object);
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\BoolProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\FloatProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\NullProvider::null()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\ObjectProvider::object()
     *
     * @param mixed $hsUrl
     */
    public function testFromObjectRejectsObjectWhenHsUrlPropertyIsNotAString($hsUrl): void
    {
        $faker = self::faker();

        $object = new \stdClass();

        $object->access_token = $faker->sha1();
        $object->element_url = $faker->url();
        $object->hs_url = $hsUrl;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Configuration "%s" should be a string, got "%s" instead.',
            'hs_url',
            is_object($hsUrl) ? get_class($hsUrl) : gettype($hsUrl),
        ));

        Matrix\Application\Configuration::fromObject($object);
    }

    public function testFromObjectReturnsConfiguration(): void
    {
        $faker = self::faker();

        $object = new \stdClass();

        $object->access_token = $faker->sha1();
        $object->element_url = $faker->url();
        $object->hs_url = $faker->url();

        $configuration = Matrix\Application\Configuration::fromObject($object);

        self::assertSame($object->access_token, $configuration->accessToken());
        self::assertSame($object->element_url, $configuration->elementUrl());
        self::assertSame($object->hs_url, $configuration->hsUrl());
    }
}
