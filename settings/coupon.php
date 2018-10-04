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
 * Add/update Coupons 
 *
 * @package    local_educart
 * @copyright  2017 sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_login();
require_once('coupon_form.php');
$couponid = optional_param('id', array(), PARAM_INT);
$setcouponid = $couponid;

require_once($CFG->dirroot.'/local/educart/lib.php');
GLOBAL $DB, $OUTPUT;
$PAGE->set_context(context_system::instance());
$PAGE->navbar->add(get_string("pluginname", "local_educart"));
$PAGE->navbar->add(get_string("settings_coupon", "local_educart"), new moodle_url('/local/educart/settings/coupons.php'));
$PAGE->set_url('/local/educart/settings/coupon.php');
$PAGE->set_title(get_string("settings_coupon", "local_educart"));
$PAGE->set_pagelayout('standard');

if (isset($couponid) && !empty($couponid)) {
	$PAGE->navbar->add(get_string("couponedit", "local_educart"));
	$couponid = array('couponid' => $couponid);
	$coupon_mform = new coupon_form(null, $couponid);
} else {
	$PAGE->navbar->add(get_string("couponadd", "local_educart"));
	$couponid = array('couponid' => $couponid);
	$coupon_mform = new coupon_form();
}

if ($coupon_mform->is_cancelled()) {
	redirect($CFG->wwwroot.'/local/educart/settings/coupons.php');
} else if ($coupon_data = $coupon_mform->get_data()) {
	if (isset($coupon_data->id) && !empty($coupon_data->id)) {
		update_coupon($coupon_data);
	} else {
		add_coupon($coupon_data);
	}
} else {
	echo $OUTPUT->header();
	if (isset($setcouponid) && !empty($setcouponid)) {
		$coupon_data = $DB->get_record('educart_coupons', array('id' => $setcouponid));
		$coupon_mform->set_data($coupon_data);
	}
	$coupon_mform->display();
	echo $OUTPUT->footer();
}
