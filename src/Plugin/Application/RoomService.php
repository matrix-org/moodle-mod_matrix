<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Application;

use Ergebnis\Clock;
use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class RoomService
{
    private $configuration;
    private $nameService;
    private $moduleRepository;
    private $roomRepository;
    private $matrixRoomService;
    private $clock;

    public function __construct(
        Plugin\Application\Configuration $configuration,
        Plugin\Application\NameService $nameService,
        Plugin\Domain\ModuleRepository $moduleRepository,
        Plugin\Domain\RoomRepository $roomRepository,
        Matrix\Application\RoomService $matrixRoomService,
        Clock\Clock $clock
    ) {
        $this->configuration = $configuration;
        $this->nameService = $nameService;
        $this->moduleRepository = $moduleRepository;
        $this->roomRepository = $roomRepository;
        $this->matrixRoomService = $matrixRoomService;
        $this->clock = $clock;
    }

    /**
     * @throws Plugin\Domain\ModuleNotFound
     */
    public function urlForRoom(
        Plugin\Domain\Room $room,
        Matrix\Domain\UserId $userId
    ): Plugin\Domain\Url {
        if ('' === $this->configuration->elementUrl()->toString()) {
            return Plugin\Domain\Url::fromString(\sprintf(
                'https://matrix.to/#/%s',
                $room->matrixRoomId()->toString(),
            ));
        }

        if (self::isDifferentHomeserver($this->configuration->homeserverUrl(), $userId->homeserver())) {
            return Plugin\Domain\Url::fromString(\sprintf(
                'https://matrix.to/#/%s',
                $room->matrixRoomId()->toString(),
            ));
        }

        $module = $this->moduleRepository->findOneBy([
            'id' => $room->moduleId()->toInt(),
        ]);

        if (!$module instanceof Plugin\Domain\Module) {
            throw Plugin\Domain\ModuleNotFound::for($room->moduleId());
        }

        if ($module->target()->equals(Plugin\Domain\ModuleTarget::matrixTo())) {
            return Plugin\Domain\Url::fromString(\sprintf(
                'https://matrix.to/#/%s',
                $room->matrixRoomId()->toString(),
            ));
        }

        return Plugin\Domain\Url::fromString(\sprintf(
            '%s/#/room/%s',
            $this->configuration->elementUrl()->toString(),
            $room->matrixRoomId()->toString(),
        ));
    }

    public function createRoomForCourse(
        Moodle\Domain\Course $course,
        Plugin\Domain\Module $module
    ): Plugin\Domain\Room {
        $name = $this->nameService->forCourseAndModule(
            $course->shortName(),
            $module->name(),
        );

        $topic = Matrix\Domain\RoomTopic::fromString($module->topic()->toString());

        $matrixRoomId = $this->matrixRoomService->createRoom(
            $name,
            $topic,
            [
                'org.matrix.moodle.course_id' => $course->id()->toInt(),
            ],
        );

        $room = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::unknown(),
            $module->id(),
            null,
            $matrixRoomId,
            Moodle\Domain\Timestamp::fromInt($this->clock->now()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt(0),
        );

        $this->roomRepository->save($room);

        return $room;
    }

    public function createRoomForCourseAndGroup(
        Moodle\Domain\Course $course,
        Moodle\Domain\Group $group,
        Plugin\Domain\Module $module
    ): Plugin\Domain\Room {
        $name = $this->nameService->forGroupCourseAndModule(
            $group->name(),
            $course->shortName(),
            $module->name(),
        );

        $topic = Matrix\Domain\RoomTopic::fromString($module->topic()->toString());

        $matrixRoomId = $this->matrixRoomService->createRoom(
            $name,
            $topic,
            [
                'org.matrix.moodle.course_id' => $course->id()->toInt(),
                'org.matrix.moodle.group_id' => $group->id()->toInt(),
            ],
        );

        $room = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::unknown(),
            $module->id(),
            $group->id(),
            $matrixRoomId,
            Moodle\Domain\Timestamp::fromInt($this->clock->now()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt(0),
        );

        $this->roomRepository->save($room);

        return $room;
    }

    private static function isDifferentHomeserver(
        Matrix\Domain\Url $homeserverUrl,
        Matrix\Domain\Homeserver $homeserver
    ): bool {
        $host = \parse_url(
            $homeserverUrl->toString(),
            \PHP_URL_HOST,
        );

        $substr = \mb_substr($host, -1 * \mb_strlen($homeserver->toString()));

        if (\mb_strtolower($homeserver->toString()) === \mb_strtolower($substr)) {
            return false;
        }

        return true;
    }
}
