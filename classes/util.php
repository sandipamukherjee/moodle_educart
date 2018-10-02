<?php
// This file is part of Moodle - http://moodle.org/
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
 * Educart ipn utility class.
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_educart;

defined('MOODLE_INTERNAL') || die();


final class util {

    /**
     * Alerts site admin of potential problems.
     *
     * @param string   $subject email subject
     * @param stdClass $data  PayPal IPN data
     */
    public static function message_transaction_error_to_admin($subject, $data) {
        $admin = get_admin();
        $site = get_site();

        $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";

        $eventdata = new \core\message\message();
        $eventdata->courseid          = $data->courseid;
        $eventdata->modulename        = 'moodle';
        $eventdata->component         = 'local_educart';
        $eventdata->name              = 'local_educart_transaction_enrolment';
        $eventdata->userfrom          = $admin;
        $eventdata->userto            = $admin;
        $eventdata->subject           = "Cart Transaction ERROR: ".$subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }

    /**
     * Silent exception handler.
     *
     * @return callable exception handler
     */
    public static function get_exception_handler() {
        return function($ex) {
            $info = get_exception_info($ex);

            $logerrmsg = "local_educart IPN exception handler: ".$info->message;
            if (debugging('', DEBUG_NORMAL)) {
                $logerrmsg .= ' Debug: '.$info->debuginfo."\n".format_backtrace($info->backtrace, true);
            }
            error_log($logerrmsg);

            exit(0);
        };
    }
}
