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
 * Save information about cart.
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
GLOBAL $DB, $OUTPUT;

if (!empty($_POST)) {
	$add_to_cart = new stdClass();
	
	
	$is_add_to_cart = $DB->get_record('educart_cart', array('courseid' => $_POST["cart_courseid"], 'userid' => $_POST["userid"]));
	if (empty($is_add_to_cart)) {
		$add_to_cart->courseid = $_POST["cart_courseid"];
		$add_to_cart->userid = $_POST["userid"];
		$add_to_cart->price = $_POST["cart_price"];
		$add_to_cart->timecreated = time();
		$add_to_cart->status = 0;/*default*/
		$lastinsertid = $DB->insert_record('educart_cart', $add_to_cart, false);
	} else if ($is_add_to_cart->status == 1){
		$add_to_cart->id = $is_add_to_cart->id;
		$add_to_cart->courseid = $_POST["cart_courseid"];
		$add_to_cart->userid = $_POST["userid"];
		$add_to_cart->price = $_POST["cart_price"];
		$add_to_cart->timecreated = time();
		$add_to_cart->status = 0;/*default*/
		$DB->update_record('educart_cart', $add_to_cart, false);
	}
	
}
