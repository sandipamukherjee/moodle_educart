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
 * Paypal return script
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require("../../../config.php");
$orderid = required_param('custom', PARAM_RAW);
$PAGE->set_url($CFG->wwwroot.'/local/educart/shop/return.php');
GLOBAL $DB, $CFG;

if (empty($orderid)) {
	redirect($CFG->wwwroot);
} else {
	$PAGE->set_context(context_system::instance());
	require_login();
	
	$isorder = $DB->get_record('educart_order', array('id' => $orderid));

	$courses_cart_updates = explode(",", $isorder->courseids);

	foreach($courses_cart_updates as $courses_cart_update){
        $is_check_cart = $DB->get_record('educart_cart', array('courseid' => $courses_cart_update, 'userid' => $USER->id));
        $update_cart = new stdClass();
        $update_cart->id = $is_check_cart->id;
        $update_cart->courseid = $is_check_cart->courseid;
        $update_cart->userid = $is_check_cart->userid;
        $update_cart->price = $is_check_cart->price;
        $update_cart->timecreated = time();
        $update_cart->status = 1;/*cart item proceed in checkout*/
        $DB->update_record('educart_cart', $update_cart, false);
    }
    echo $OUTPUT->header();

    notice(get_string('afterpayment', 'local_educart'), $CFG->wwwroot.'/my');

}

die;