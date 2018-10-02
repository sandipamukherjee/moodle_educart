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
 * Shop page
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
GLOBAL $DB, $OUTPUT;
$PAGE->set_context(context_system::instance());
require_login();
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", "local_educart"));
$PAGE->navbar->add(get_string("shop", "local_educart"), new moodle_url('/local/educart/shop'));
$PAGE->set_url('/local/educart/shop/index.php');
$PAGE->set_title(get_string("shop", "local_educart"));
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/educart/css/bootstrap.min.css');
$PAGE->requires->css('/local/educart/css/style.css');
echo $OUTPUT->header();

$course_for_shops = $DB->get_records_sql("Select c.*, e.*, cc.name as catname from {course} as c 
	LEFT JOIN {course_categories} as cc ON c.category = cc.id 
	JOIN {enrol} as e ON c.id = e.courseid and e.status = ? and e.enrol IN ('paypal')", array(0));

$out = "";
$out .= html_writer::start_div('row educart_shop_container');
if($course_for_shops) {
	foreach ($course_for_shops as $key => $course_for_shop) {
		$coursecontext = context_course::instance($course_for_shop->courseid);
		$isfile = $DB->get_records_sql("Select * from {files} where contextid = ? and filename != ? and filearea = ?", array($coursecontext->id, ".", "overviewfiles"));
		if($isfile) {
			foreach ($isfile as $key1 => $isfilevalue) {
				$courseimage =  $CFG->wwwroot . "/pluginfile.php/" . $isfilevalue->contextid ."/". $isfilevalue->component . "/" . $isfilevalue->filearea . "/" . $isfilevalue->filename;	
			}
		}   

		$out .= html_writer::start_div('col-md-3');
		$out .= html_writer::start_tag('a', array('href' => 'javascript:void(0)', 'class' => 'shop_link'));
			$out .= html_writer::start_div('course_grid');
				$out .= html_writer::start_div('course_grid_img');
					if(!empty($courseimage)) {
						$out .= html_writer::empty_tag('img', array('src' => $courseimage, 'alt' => ""));
					} else {
						$out .= html_writer::empty_tag('img', array('src' => $CFG->wwwroot."/local/educart/pix/nopic.jpg", 'alt' => ""));
					}
				$out .= html_writer::end_div();

				$out .= html_writer::start_tag('h3');
				$out .= $course_for_shop->fullname;
				$out .= html_writer::end_tag('h3');
				$out .= html_writer::start_tag('div', array('class' => 'course_category'));
				$out .= $course_for_shop->catname;
				$out .= html_writer::end_tag('div');
				$out .= html_writer::start_tag('div', array('class' => 'course_price'));
				$out .= $course_for_shop->currency ." ". $course_for_shop->cost;
				$out .= html_writer::end_tag('div');
				if (!is_enrolled($coursecontext, $USER->id)){
					$is_already_in_cart = $DB->get_record('educart_cart', array('courseid' => $course_for_shop->courseid, 'userid' => $USER->id, 'status' => 0));
					
					if (!empty($is_already_in_cart)) {
						$out .= html_writer::start_tag('a', array('href' => $CFG->wwwroot.'/local/educart/shop/cart.php', 'id' => 'view_cart_'.$course_for_shop->courseid, 'class' => 'educart_button view_cart'));
						$out .= get_string("view_cart", "local_educart");
						$out .= html_writer::end_tag('a');
					} else {
						$out .= html_writer::start_tag('a', array('href' => 'javascript:void(0)', 'id' => 'add_to_cart_'.$course_for_shop->courseid, 'class' => 'add_to_cart educart_button', 'userid' => $USER->id, 'cart-courseid' => $course_for_shop->courseid, 'cart-price' => $course_for_shop->cost, 'post-url' => $CFG->wwwroot.'/local/educart/shop/add_cart.php'));
					
						$out .= get_string("add_to_cart", "local_educart");
						$out .= html_writer::end_tag('a');
						$out .= html_writer::start_tag('a', array('href' => $CFG->wwwroot.'/local/educart/shop/cart.php', 'id' => 'view_cart_'.$course_for_shop->courseid, 'style' => 'display : none', 'class' => 'educart_button view_cart'));
						$out .= get_string("view_cart", "local_educart");
						$out .= html_writer::end_tag('a');
					}
					
					/*$out .= html_writer::start_tag('a', array('href' => 'buy_now.php?id='.$course_for_shop->courseid, 'class' => 'buy_now educart_button'));
					$out .= get_string("buy_now", "local_educart");
					$out .= html_writer::end_tag('a');*/
				} else {
					$out .= html_writer::start_tag('a', array('href' => $CFG->wwwroot."/course/view.php?id=".$course_for_shop->courseid, 'class' => 'add_to_cart educart_button'));
					$out .= get_string("viewcourse", "local_educart");
					$out .= html_writer::end_tag('a');
				}
				
			$out .= html_writer::end_div();
		$out .= html_writer::end_tag('a');
		$out .= html_writer::end_div();
	}
} else {
	$out .= html_writer::start_tag('div', array('class' => 'empty_shop'));
	$out .= get_string("empty_shop", "local_educart");
	$out .= html_writer::end_tag('div');
}
$out .= html_writer::end_div();

echo $out;
$PAGE->requires->js('/local/educart/js/jquery.js');
$PAGE->requires->js('/local/educart/js/cart.js');
echo $OUTPUT->footer();
