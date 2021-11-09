<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Infrastructure;

use Curl\Curl;
use mod_matrix\Matrix;

final class CurlBasedApi implements Matrix\Application\Api
{
    private $hsUrl;
    private $accessToken;

    public function __construct(
        string $hsUrl,
        string $accessToken
    ) {
        $this->hsUrl = $hsUrl;
        $this->accessToken = $accessToken;
    }

    public function whoami(): Matrix\Domain\UserId
    {
        $r = $this->request(
            'GET',
            '/_matrix/client/r0/account/whoami',
        );

        return Matrix\Domain\UserId::fromString($r['user_id']);
    }

    public function createRoom($opts = []): Matrix\Domain\RoomId
    {
        $r = $this->request(
            'POST',
            '/_matrix/client/r0/createRoom',
            [],
            $opts,
        );

        return Matrix\Domain\RoomId::fromString($r['room_id']);
    }

    public function inviteUser(
        Matrix\Domain\UserId $userId,
        Matrix\Domain\RoomId $roomId
    ) {
        return $this->request(
            'POST',
            sprintf(
                '/_matrix/client/r0/rooms/%s/invite',
                urlencode($roomId->toString()),
            ),
            [],
            [
                'user_id' => $userId->toString(),
            ],
        );
    }

    public function kickUser(
        Matrix\Domain\UserId $userId,
        Matrix\Domain\RoomId $roomId
    ) {
        return $this->request(
            'POST',
            sprintf(
                '/_matrix/client/r0/rooms/%s/kick',
                urlencode($roomId->toString()),
            ),
            [],
            [
                'user_id' => $userId->toString(),
            ],
        );
    }

    public function getState(
        Matrix\Domain\RoomId $roomId,
        $eventType,
        $stateKey
    ) {
        return $this->request(
            'GET',
            sprintf(
                '/_matrix/client/r0/rooms/%s/state/%s/%s',
                urlencode($roomId->toString()),
                urlencode($eventType),
                urlencode($stateKey),
            ),
        );
    }

    public function setState(
        Matrix\Domain\RoomId $roomId,
        $eventType,
        $stateKey,
        $content
    ) {
        return $this->request(
            'PUT',
            sprintf(
                '/_matrix/client/r0/rooms/%s/state/%s/%s',
                urlencode($roomId->toString()),
                urlencode($eventType),
                urlencode($stateKey),
            ),
            [],
            $content,
        );
    }

    public function getMembersOfRoom(Matrix\Domain\RoomId $roomId): array
    {
        $members = $this->request(
            'GET',
            sprintf(
                '/_matrix/client/r0/rooms/%s/members',
                urlencode($roomId->toString()),
            ),
        );

        $userIds = [];

        foreach ($members['chunk'] as $ev) {
            if ($ev['content'] && $ev['content']['membership']) {
                $membership = $ev['content']['membership'];

                if ('join' === $membership || 'invite' === $membership) {
                    $userIds[] = Matrix\Domain\UserId::fromString($ev['state_key']);
                }
            }
        }

        return $userIds;
    }

    public function debug($val): void
    {
        $val = var_export($val, true);

        $this->request(
            'PUT',
            '/_matrix/client/r0/rooms/!cujtuCldotJLtvQGiQ:localhost/send/m.room.message/m' . microtime() . 'r' . mt_rand(0, 100),
            [],
            [
                'msgtype' => 'm.text',
                'body' => $val,
            ],
        );
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function request(
        string $method,
        string $path,
        array $qs = [],
        array $body = []
    ) {
        $allowedMethods = [
            'GET',
            'POST',
            'PUT',
        ];

        if (!in_array($method, $allowedMethods, true)) {
            throw new \InvalidArgumentException('unknown method: ' . $method);
        }

        $curl = new Curl();
        $curl->setDefaultJsonDecoder(true);
        $curl->setHeader('Authorization', 'Bearer ' . $this->accessToken);
        $curl->setHeader('Content-Type', 'application/json');

        if ('GET' === $method) {
            $curl->get($this->hsUrl . $path, $qs);
        } elseif ('POST' === $method) {
            $curl->setUrl($this->hsUrl . $path, $qs);
            $curl->post($curl->getUrl(), $body);
        } elseif ('PUT' === $method) {
            $curl->setUrl($this->hsUrl . $path, $qs);
            $curl->put($curl->getUrl(), $body);
        }

        if ($curl->error) {
            $httpStatusCode = $curl->httpStatusCode;
            $httpErrorMessage = $curl->httpErrorMessage;

            if (
                is_array($curl->response)
                && array_key_exists('errcode', $curl->response)
                && array_key_exists('error', $curl->response)
            ) {
                $errorCode = $curl->response['errcode'];
                $errorMessage = $curl->response['error'];

                throw new \RuntimeException(
                    <<<TXT
Sending a request failed with HTTP status code ${httpStatusCode} and error message ${httpErrorMessage}.

The response contains a specific error code and message.

Error code
---------

${errorCode}

Error message
---------

${errorMessage}

TXT
                );
            }

            throw new \RuntimeException(
                <<<TXT
Sending a request failed with HTTP status code ${httpStatusCode} and error message ${httpErrorMessage}.
TXT
            );
        }

        return $curl->response;
    }
}
