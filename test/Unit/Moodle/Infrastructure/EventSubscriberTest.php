<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Infrastructure;

use core\event;
use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Infrastructure\EventSubscriber
 */
final class EventSubscriberTest extends Framework\TestCase
{
    public function testObserversReturnsObservers(): void
    {
        $expected = self::expectedObservers();

        self::assertEquals($expected, Moodle\Infrastructure\EventSubscriber::observers());
    }

    /**
     * @dataProvider provideObserverClassAndMethodName
     */
    public function testObserversAreCallable(string $className, string $methodName): void
    {
        self::assertTrue(\class_exists($className), \sprintf(
            'Failed asserting that class "%s" exists.',
            $className,
        ));

        $reflection = new \ReflectionClass($className);

        self::assertTrue($reflection->hasMethod($methodName), \sprintf(
            'Failed asserting that class "%s" has method "%s".',
            $className,
            $methodName,
        ));

        $method = $reflection->getMethod($methodName);

        self::assertFalse($method->isAbstract(), \sprintf(
            'Failed asserting that class "%s" has method "%s" that is not abstract.',
            $className,
            $methodName,
        ));

        self::assertTrue($method->isPublic(), \sprintf(
            'Failed asserting that class "%s" has method "%s" that is public.',
            $className,
            $methodName,
        ));

        self::assertTrue($method->isStatic(), \sprintf(
            'Failed asserting that class "%s" has method "%s" that is static.',
            $className,
            $methodName,
        ));
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public function provideObserverClassAndMethodName(): \Generator
    {
        foreach (self::expectedObservers() as $observer) {
            [$className, $methodName] = $observer['callback'];

            yield $observer['eventname'] => [
                $className,
                $methodName,
            ];
        }
    }

    private static function expectedObservers(): array
    {
        return [
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onCourseUpdated',
                ],
                'eventname' => event\course_updated::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onGroupCreated',
                ],
                'eventname' => event\group_created::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onGroupMemberAdded',
                ],
                'eventname' => event\group_member_added::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onGroupMemberRemoved',
                ],
                'eventname' => event\group_member_removed::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onRoleAssigned',
                ],
                'eventname' => event\role_assigned::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onRoleCapabilitiesUpdated',
                ],
                'eventname' => event\role_capabilities_updated::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onRoleDeleted',
                ],
                'eventname' => event\role_deleted::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onRoleUnassigned',
                ],
                'eventname' => event\role_unassigned::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onUserEnrolmentCreated',
                ],
                'eventname' => event\user_enrolment_created::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onUserEnrolmentDeleted',
                ],
                'eventname' => event\user_enrolment_deleted::class,
                'internal' => false,
            ],
            [
                'callback' => [
                    Moodle\Infrastructure\EventSubscriber::class,
                    'onUserEnrolmentUpdated',
                ],
                'eventname' => event\user_enrolment_updated::class,
                'internal' => false,
            ],
        ];
    }
}
