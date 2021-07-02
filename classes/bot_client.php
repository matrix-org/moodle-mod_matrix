<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

defined('MOODLE_INTERNAL') || exit;

global $CFG;

require_once $CFG->dirroot . '/mod/matrix/locallib.php';

require $CFG->dirroot . '/mod/matrix/vendor/autoload.php';

class moodle_matrix_bot
{
    private $baseurl;

    private $access_token;

    public function __construct(
        string $baseurl,
        string $access_token
    ) {
        $this->baseurl = $baseurl;
        $this->access_token = $access_token;
    }

    public function whoami()
    {
        $r = $this->req('GET', '/_matrix/client/r0/account/whoami');

        return $r['user_id'];
    }

    public function create_room($opts = [])
    {
        $r = $this->req('POST', '/_matrix/client/r0/createRoom', [], $opts);

        return $r['room_id'];
    }

    public function invite_user($user_id, $room_id)
    {
        return $this->req('POST', '/_matrix/client/r0/rooms/' . urlencode($room_id) . '/invite', [], [
            'user_id' => $user_id,
        ]);
    }

    public function kick_user($user_id, $room_id)
    {
        return $this->req('POST', '/_matrix/client/r0/rooms/' . urlencode($room_id) . '/kick', [], [
            'user_id' => $user_id,
        ]);
    }

    public function get_state($room_id, $event_type, $state_key)
    {
        return $this->req('GET', '/_matrix/client/r0/rooms/' . urlencode($room_id) . '/state/' . urlencode($event_type) . '/' . urlencode($state_key));
    }

    public function set_state($room_id, $event_type, $state_key, $content)
    {
        return $this->req('PUT', '/_matrix/client/r0/rooms/' . urlencode($room_id) . '/state/' . urlencode($event_type) . '/' . urlencode($state_key), [], $content);
    }

    public function get_effective_joins($room_id)
    {
        $members = $this->req('GET', '/_matrix/client/r0/rooms/' . urlencode($room_id) . '/members');
        $user_ids = [];

        foreach ($members['chunk'] as $ev) {
            if ($ev['content'] && $ev['content']['membership']) {
                $membership = $ev['content']['membership'];

                if ('join' == $membership || 'invite' == $membership) {
                    $user_ids[] = $ev['state_key'];
                }
            }
        }

        return $user_ids;
    }

    public function debug($val)
    {
        $val = var_export($val, true);
        $this->req('PUT', '/_matrix/client/r0/rooms/!cujtuCldotJLtvQGiQ:localhost/send/m.room.message/m' . microtime() . 'r' . mt_rand(0, 100), [], [
            'msgtype' => 'm.text',
            'body' => $val,
        ]);
    }

    /**
     * @throws \RuntimeException
     */
    public static function instance(): self
    {
        $config = get_config('mod_matrix');

        if (!property_exists($config, 'hs_url')) {
            throw new \RuntimeException(sprintf(
                'Module configuration should have a "%s" property, but it does not.',
                'hs_url'
            ));
        }

        $hsUrl = $config->hs_url;

        if (!is_string($hsUrl)) {
            throw new \RuntimeException(sprintf(
                'Module configuration "%s" should be a string, got "%s" instead..',
                'hs_url',
                is_object($hsUrl) ? get_class($hsUrl) : gettype($hsUrl)
            ));
        }

        if (!property_exists($config, 'access_token')) {
            throw new \RuntimeException(sprintf(
                'Module configuration should have a "%s" property, but it does not.',
                'access_token'
            ));
        }

        $accessToken = $config->access_token;

        if (!is_string($accessToken)) {
            throw new \RuntimeException(sprintf(
                'Module configuration "%s" should be a string, got "%s" instead..',
                'access_token',
                is_object($accessToken) ? get_class($accessToken) : gettype($accessToken)
            ));
        }

        return new self(
            $hsUrl,
            $accessToken
        );
    }

    /**
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    private function req(string $method, string $path, array $qs = [], array $body = [])
    {
        $allowedMethods = [
            'GET',
            'POST',
            'PUT',
        ];

        if (!in_array($method, $allowedMethods, true)) {
            throw new \InvalidArgumentException('unknown method: ' . $method);
        }

        $curl = new \Curl\Curl();
        $curl->setDefaultJsonDecoder($assoc = true);
        $curl->setHeader('Authorization', 'Bearer ' . $this->access_token);
        $curl->setHeader('Content-Type', 'application/json');

        if ('GET' === $method) {
            $curl->get($this->baseurl . $path, $qs);
        } elseif ('POST' === $method) {
            $curl->setUrl($this->baseurl . $path, $qs);
            $curl->post($curl->getUrl(), $body);
        } elseif ('PUT' === $method) {
            $curl->setUrl($this->baseurl . $path, $qs);
            $curl->put($curl->getUrl(), $body);
        }

        if ($curl->error) {
            throw new \Exception('request failed - Code: ' . $curl->errorCode . ' Message: ' . $curl->errorMessage);
        }

        return $curl->response;
    }
}
