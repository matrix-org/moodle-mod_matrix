<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_matrix;

defined('MOODLE_INTERNAL') || exit;

global $CFG;

require_once $CFG->dirroot . '/mod/matrix/locallib.php';

require $CFG->dirroot . '/mod/matrix/vendor/autoload.php';

class moodle_matrix_bot
{
    private $access_token;

    private $baseurl;

    public function __construct()
    {
        $conf = get_config('mod_matrix');
        $this->baseurl = $conf->{'hs_url'};
        $this->access_token = $conf->{'access_token'};
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
            "user_id" => $user_id,
        ]);
    }

    public function kick_user($user_id, $room_id)
    {
        return $this->req('POST', '/_matrix/client/r0/rooms/' . urlencode($room_id) . '/kick', [], [
            "user_id" => $user_id,
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

                if ($membership == 'join' || $membership == 'invite') {
                    $user_ids[] = $ev['state_key'];
                }
            }
        }

        return $user_ids;
    }

    public function debug($val)
    {
        $val = var_export($val, true);
        $this->req('PUT', '/_matrix/client/r0/rooms/!cujtuCldotJLtvQGiQ:localhost/send/m.room.message/m' . microtime() . 'r' . rand(0, 100), [], [
            "msgtype" => "m.text",
            "body" => $val,
        ]);
    }

    private function req($method, $path, $qs = [], $body = [])
    {
        $curl = new \Curl\Curl();
        $curl->setDefaultJsonDecoder($assoc = true);
        $curl->setHeader('Authorization', 'Bearer ' . $this->access_token);
        $curl->setHeader('Content-Type', 'application/json');

        if ($method == 'GET') {
            $curl->get($this->baseurl . $path, $qs);
        } elseif ($method == 'POST') {
            $curl->setUrl($this->baseurl . $path, $qs);
            $curl->post($curl->getUrl(), $body);
        } elseif ($method == 'PUT') {
            $curl->setUrl($this->baseurl . $path, $qs);
            $curl->put($curl->getUrl(), $body);
        } else {
            throw new \Exception("unknown method: " . $method);
        }

        if ($curl->error) {
            throw new \Exception("request failed - Code: " . $curl->errorCode . " Message: " . $curl->errorMessage);
        }

        return $curl->response;
    }

    public static function instance(): moodle_matrix_bot
    {
        return new moodle_matrix_bot();
    }
}
