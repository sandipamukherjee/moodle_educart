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
 * Add course in shop with price, tax, discount
 *
 * @package    local_educart
 * @copyright  2017 sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once('add_course_in_shop_form.php');

$courseid = optional_param('id', 0, PARAM_INT);
GLOBAL $DB, $OUTPUT;

$PAGE->set_url('/local/educart/settings/add_course_in_shop.php');

if(($courseid!=0)) {
	$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
	require_login($course, true);
	$PAGE->set_title(get_string("titleaddcourseinshop", "local_educart", $course->fullname));
	$PAGE->set_pagelayout('standard');
	$PAGE->set_context(context_course::instance($courseid));
} else {
	$PAGE->set_context(context_system::instance());
}

echo $OUTPUT->header();

$courseid = array('courseid' => $courseid);
$mform = new course_price_form(null, $courseid);

if(isset($course)) {
	$out = '';
	$out .= html_writer::start_div('row educart_course_setup_container');
	$out .= html_writer::start_div('span12');
	$out .= html_writer::start_div('educart_course_name');
	$out .= $course->fullname;
	$out .= html_writer::end_div();
	$out .= html_writer::start_div('educart_course_summary');
	$out .= $course->summary;
	$out .= html_writer::end_div();
	$out .= html_writer::end_div();
	$out .= html_writer::end_div();//end of educart_course_setup_container
	echo $out;

	//Form processing and displaying is done here
	
}
if ($mform->is_cancelled()) {
	    //Handle form cancel operation, if cancel button is present on form
	} else if ($get_price_form = $mform->get_data()) { 
		
		$course_price_record = new stdClass();
		$course_price_record->courseid = $get_price_form->courseid;
		$course_price_record->price = $get_price_form->price;
		$course_price_record->tax = $get_price_form->tax;
		$course_price_record->discount = $get_price_form->discount;
		$is_course_price = $DB->get_record('educart_course_price', array('courseid' => $get_price_form->courseid));
		if (empty($is_course_price)) {
			$lastinsertid = $DB->insert_record('educart_course_price', $course_price_record, false);
		} else {
			$course_price_record->id = $is_course_price->id;
			$lastupdateid = $DB->update_record('educart_course_price', $course_price_record, false);
		}
		$return_url = new moodle_url('/local/educart/settings/add_course_in_shop.php', array('id' => $get_price_form->courseid));
		redirect($return_url);
	} else {
		$mform->display();
	}
echo $OUTPUT->footer();

