<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_matrix;

use core_competency\url;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/matrix/locallib.php');
require($CFG->dirroot.'/mod/matrix/vendor/autoload.php');

class moodle_matrix_bot {
    private $access_token;
    private $baseurl;
    
    public function __construct() {
        $conf = get_config('mod_matrix');
        $this->baseurl = $conf->{'hs_url'};
        $this->access_token = $conf->{'access_token'};
    }

    public function whoami() {
        $r = $this->req('GET', '/_matrix/client/r0/account/whoami');
        return $r['user_id'];
    }

    public function create_room($opts = array()) {
        $r = $this->req('POST', '/_matrix/client/r0/createRoom', array(), $opts);
        return $r['room_id'];
    }

    public function invite_user($user_id, $room_id) {
        return $this->req('POST', '/_matrix/client/r0/rooms/'.urlencode($room_id).'/invite', array(), array(
            "user_id" => $user_id,
        ));
    }

    public function kick_user($user_id, $room_id) {
        return $this->req('POST', '/_matrix/client/r0/rooms/'.urlencode($room_id).'/kick', array(), array(
            "user_id" => $user_id,
        ));
    }

    public function get_state($room_id, $event_type, $state_key) {
        return $this->req('GET', '/_matrix/client/r0/rooms/'.urlencode($room_id).'/state/'.urlencode($event_type).'/'.urlencode($state_key));
    }

    public function set_state($room_id, $event_type, $state_key, $content) {
        return $this->req('PUT', '/_matrix/client/r0/rooms/'.urlencode($room_id).'/state/'.urlencode($event_type).'/'.urlencode($state_key), array(), $content);
    }

    public function get_effective_joins($room_id) {
        $members = $this->req('GET', '/_matrix/client/r0/rooms/'.urlencode($room_id).'/members');
        $user_ids = array();
        foreach ($members['chunk'] as $ev) {
            if ($ev['content'] && $ev['content']['membership']) {
                $membership = $ev['content']['membership'];
                if ($membership == 'join' || $membership == 'invite') {
                    array_push($user_ids, $ev['state_key']);
                }
            }
        }
        return $user_ids;
    }

    public function debug($val) {
        $val = var_export($val, true);
        $this->req('PUT', '/_matrix/client/r0/rooms/!cujtuCldotJLtvQGiQ:localhost/send/m.room.message/m'.microtime().'r'.rand(0, 100), array(), array(
            "msgtype" => "m.text",
            "body" => $val,
        ));
    }

    private function req($method, $path, $qs = array(), $body = array()) {
        $curl = new \Curl\Curl();
        $curl->setDefaultJsonDecoder($assoc = true);
        $curl->setHeader('Authorization', 'Bearer '.$this->access_token);
        $curl->setHeader('Content-Type', 'application/json');
        if ($method == 'GET') {
            $curl->get($this->baseurl.$path, $qs);
        } elseif ($method == 'POST') {
            $curl->setUrl($this->baseurl.$path, $qs);
            $curl->post($curl->getUrl(), $body);
        } elseif ($method == 'PUT') {
            $curl->setUrl($this->baseurl.$path, $qs);
            $curl->put($curl->getUrl(), $body);
        } else {
            throw new \Exception("unknown method: ".$method);
        }
        if ($curl->error) {
            throw new \Exception("request failed - Code: ".$curl->errorCode." Message: ".$curl->errorMessage);
        }
        return $curl->response;
    }

    public static function instance(): moodle_matrix_bot {
        return new moodle_matrix_bot();
    }
}