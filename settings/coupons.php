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
 * Coupons listing
 *
 * @package    local_educart
 * @copyright  2017 sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_login();
//require_once('coupons_form.php');

GLOBAL $DB, $OUTPUT;
$PAGE->set_context(context_system::instance());
$PAGE->navbar->add(get_string("administrationsite"), new moodle_url('/admin/search.php'));
$PAGE->navbar->add(get_string("pluginname", "local_educart"), new moodle_url('/admin/category.php?category=local_educart'));
$PAGE->navbar->add(get_string("settings_coupon", "local_educart"), new moodle_url('/local/educart/settings/coupons.php'));
$PAGE->set_url('/local/educart/settings/coupons.php');
$PAGE->set_title(get_string("settings_coupon", "local_educart"));
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/educart/css/style.css');
echo $OUTPUT->header();

$coupons = $DB->get_records('educart_coupons', array('status' => 1));


$out = html_writer::start_div('coupon_main');
$out .= html_writer::start_tag('a', array('href' => $CFG->wwwroot.'/local/educart/settings/coupon.php', 'id' => 'add_coupon', 'class' => 'educart_button'));
$out .= get_string('add_coupon', 'local_educart');
$out .= html_writer::end_tag('a');
$table = new html_table();
$table->head  = array(get_string('coupon_name', 'local_educart'), get_string('coupon_desc', 'local_educart'), get_string('coupon_type', 'local_educart'), get_string('coupon_amount', 'local_educart'), get_string('coupon_applied_on_product_ids', 'local_educart'), get_string('coupon_usage_limit', 'local_educart'), get_string('expiry_date', 'local_educart'), "");
$table->attributes['class'] = 'coupon_table generaltable';
$table->id = 'coupon_table_id';
//$table->align = array('center', 'left', 'left');

$table->data  = array();
foreach ($coupons as $key => $coupon) {
	$out_coupon_name = html_writer::start_div('coupon_name');
	$out_coupon_name .= $coupon->coupon_name;
	$out_coupon_name .= html_writer::end_div();
	$out_coupon_name .= html_writer::start_div('coupon_action');
	$out_coupon_name .= html_writer::start_tag('a', array('href' => $CFG->wwwroot.'/local/educart/settings/coupon.php?id='.$coupon->id));
	$out_coupon_name .= get_string('edit', 'local_educart');
	$out_coupon_name .= html_writer::end_tag('a');
	$out_coupon_name .= "|";
	$out_coupon_name .= html_writer::start_tag('a', array('href' => $CFG->wwwroot.'/local/educart/settings/coupondelete.php?id='.$coupon->id));
	$out_coupon_name .= get_string('delete', 'local_educart');
	$out_coupon_name .= html_writer::end_tag('a');
	$out_coupon_name .= html_writer::end_div();

	$out_coupon_desc = $coupon->coupon_desc;
	if ($coupon->discount_type == "fixed_cart") {
		$out_coupon_type = get_string('fixed_cart', 'local_educart');
	} else if ($coupon->discount_type == "percent") {
		$out_coupon_type = get_string('percent', 'local_educart');
	} else if ($coupon->discount_type == "fixed_product") {
		$out_coupon_type = get_string('fixed_product', 'local_educart');
	}
	
	$out_coupon_amount = $coupon->coupon_amount;
	if ($coupon->coupon_for_course != "-") {
		$allowed_courses = $DB->get_records_sql('Select id, fullname from {course} where visible = 1 and id in ('.$coupon->coupon_for_course.')');
	} else {
		$allowed_courses = '';
	}

	$out_coupon_product = html_writer::start_div('allowed_course');
	$allowed_course_count = 1;
	if (isset($allowed_courses) && !empty($allowed_courses)) {
		foreach ($allowed_courses as $allowed_course) {
			$out_coupon_product .= $allowed_course->fullname;
			if ($allowed_course_count < count($allowed_courses)) {
				$out_coupon_product .= ', ';
			}
			$allowed_course_count = $allowed_course_count + 1;
		}
	}
	$out_coupon_product .= html_writer::end_div();
	
	$out_coupon_product .= html_writer::start_div('not_allowed_course');
	if ($coupon->coupon_for_exclude_course != "-") {
		$not_allowed_courses = $DB->get_records_sql('Select id, fullname from {course} where visible = ? and id in ('.$coupon->coupon_for_exclude_course.')', array(1));
	} else {
		$not_allowed_courses = '';
	}
	$not_allowed_course_count = 1;
	if (isset($not_allowed_courses) && !empty($not_allowed_courses)) {
		foreach ($not_allowed_courses as $not_allowed_course) {
			$out_coupon_product .= $not_allowed_course->fullname;
			if ($not_allowed_course_count < count($not_allowed_courses)) {
				$out_coupon_product .= ', ';
			}
			$allowed_course_count = $not_allowed_course_count + 1;
		}
	} 
	$out_coupon_product .= html_writer::end_div();
	$out_coupon_expiry = date("M d, Y", $coupon->coupon_expiry);
	$table->data[] = array($out_coupon_name, $out_coupon_desc, $out_coupon_type, $out_coupon_amount, $out_coupon_product, "", $out_coupon_expiry, "");
}
$out .= html_writer::table($table);
$out .= html_writer::end_div();
echo $out;

echo $OUTPUT->footer();

