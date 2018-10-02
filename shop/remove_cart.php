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
 * remove from cart.
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
GLOBAL $DB, $OUTPUT;
if (!empty($_POST)) {
	print_r($_POST);
	$is_cart = $DB->get_record('educart_cart', array('courseid' => $_POST["remove_cart_courseid"], 'userid' => $_POST["userid"]));
	if (!empty($is_cart)) {
		$remove_from_cart = new stdClass();
		$remove_from_cart->id = $is_cart->id;
		$add_to_cart->courseid = $is_cart->courseid;
		$add_to_cart->userid = $is_cart->userid;
		$add_to_cart->price = $is_cart->price;
		$add_to_cart->timecreated = $is_cart->timecreated;
		$remove_from_cart->status = 1; /*if remove item from cart*/
		$DB->update_record('educart_cart', $remove_from_cart, $bulk=false);
	}
}