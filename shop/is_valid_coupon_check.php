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
 *  Coupon validity check
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
GLOBAL $DB;
$tocheckcoupon = optional_param('coupon', array(), PARAM_RAW);

if (isset($tocheckcoupon) && !empty($tocheckcoupon)) { 
	$isvalidcoupon = $DB->get_record('educart_coupons', array('coupon_name' => $tocheckcoupon, 'status' => 1));

	if (isset($isvalidcoupon) && !empty($isvalidcoupon)) {
		if (time() <= $isvalidcoupon->coupon_expiry) {
			echo 1;
		} else {
			echo 0;
		}
	}
}

$couponapplied = optional_param('couponapplied', array(), PARAM_RAW);
$userwhoapply = optional_param('user', array(), PARAM_RAW);


if (isset($couponapplied) && !empty($couponapplied) && isset($userwhoapply) && !empty($userwhoapply)) {
	$mycoursesincart = $DB->get_records('educart_cart', array('userid' => $userwhoapply, 'status' => 0));
	$excludecourseforthiscoupon = $coupondesc = $DB->get_record('educart_coupons', array('coupon_name' => $couponapplied, 'status' => 1));

	$arrmycourseincart = array();
	$mycourseincartprice = 0;
	foreach ($mycoursesincart as $mycourseincart) {
		$arrmycourseincart[] = $mycourseincart->courseid;
		$mycourseincartprice += $mycourseincart->price;
	}

	if ($coupondesc->minimum_spend > 0 && $mycourseincartprice < $coupondesc->minimum_spend ) {
		echo '0-'.get_string('minspend', 'local_educart', $coupondesc->minimum_spend);
	} else if ($coupondesc->maximum_spend > 0 && $mycourseincartprice > $coupondesc->maximum_spend ) {
		echo '0-'.get_string('maxspend', 'local_educart', $coupondesc->maximum_spend);
	} else if ($notapplicablecourseids = array_intersect(explode(',', $excludecourseforthiscoupon->coupon_for_exclude_course), $arrmycourseincart)) {
		$notapplicablecoursesnames = $DB->get_records_sql('SELECT fullname FROM {course} WHERE id IN ('.implode(",", $notapplicablecourseids).')');
		$comma_count = 1;
		echo '0-'.get_string('notapplicableforproductmsg', 'local_educart');
		foreach ($notapplicablecoursesnames as $notapplicablecoursesname) {
			echo $notapplicablecoursesname->fullname;
			if ($comma_count < count($notapplicablecoursesnames)) {
				echo ", ";
			} else if ($comma_count == count($notapplicablecoursesnames)) {
				echo ". ";
			}
			$comma_count = $comma_count + 1;
		}
	} else { 
		if ($coupondesc->discount_type == "fixed_cart") {
			echo '1-'.$coupondesc->coupon_amount;
		} else if ($coupondesc->discount_type == "percent") { 
			echo '2-'.$coupondesc->coupon_amount;
		}
	}
	
}