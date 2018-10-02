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
 * cart page
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
GLOBAL $DB, $OUTPUT, $USER;
$PAGE->set_context(context_system::instance());
require_login();
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", "local_educart"));
$PAGE->navbar->add(get_string("cart", "local_educart"), new moodle_url('/local/educart/shop/cart.php'));
$PAGE->set_url('/local/educart/shop/index.php');
$PAGE->set_title(get_string("cart", "local_educart"));
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/educart/css/bootstrap.min.css');
$PAGE->requires->css('/local/educart/css/style.css');
$course_id = optional_param('id', 0, PARAM_INT);

echo $OUTPUT->header();
if(isset($_POST) && !empty($_POST)){
print_r($_POST);die;
}

$get_my_carts = $DB->get_records('educart_cart', array('userid' => $USER->id, 'status' => 0));

$out = html_writer::start_div('cart_main');
$out .= html_writer::start_div('cart_list');
	$out .= html_writer::start_tag('h3');
	$out .= get_string('my_cart', 'local_educart');
	if (count($get_my_carts) > 0) {
		$out .= html_writer::end_tag('h3');
		$table = new html_table();
		$table->head  = array(get_string('product_image', 'local_educart'), get_string('product_details', 'local_educart'), get_string('regular_price', 'local_educart'), get_string('total_price', 'local_educart'), "");
		$table->attributes['class'] = 'cart_table generaltable';
		$table->id = 'cart_table_id';
		$table->align = array('center', 'left', 'left');
		$out .= html_writer::start_div('cart_item');
		$table->data  = array();
		$course_purchase = array();
		$total_payble_amount = 0;
		$total_item = 0;
		foreach ($get_my_carts as $get_my_cart_key => $get_my_cart) {

			$courseimage = "";
			$cart_course_context = context_course::instance($get_my_cart->courseid);
			//echo $get_my_cart->courseid;
			$course_details = $DB->get_record_sql("Select c.*, cc.name as catname, e.currency as coursecurrency, e.cost as courseprice from {course} as c 
			JOIN {course_categories} as cc ON c.category = cc.id 
			JOIN {enrol} as e ON c.id = e.courseid  
			and c.id = ? and e.status = ? and e.enrol IN ('paypal')", array($get_my_cart->courseid, 0));
			
			if(!empty($course_details)) {
				$course_purchase[] = $get_my_cart->courseid;
				$cart_price = $DB->get_record("educart_cart", array("courseid" => $get_my_cart->courseid, "userid" => $USER->id));
				$isfile = $DB->get_records_sql("Select * from {files} where contextid = ? and filename != ? and filearea = ?", array($cart_course_context->id, ".", "overviewfiles"));
				
				if($isfile) {
					foreach ($isfile as $key1 => $isfilevalue) {
						$courseimage =  $CFG->wwwroot . "/pluginfile.php/" . $isfilevalue->contextid ."/". $isfilevalue->component . "/" . $isfilevalue->filearea . "/" . $isfilevalue->filename;	
					}
				} 
				$product_image_out = html_writer::start_tag('a', array('href' => '#', 'class' => 'course_link'));
				if(!empty($courseimage)) {
					$product_image_out .= html_writer::empty_tag('img', array('src' => $courseimage, 'alt' => ""));
				} else {
					$product_image_out .= html_writer::empty_tag('img', array('src' => $CFG->wwwroot."/local/educart/pix/nopic.jpg", 'alt' => ""));
				}
				$product_image_out .= html_writer::end_tag('a');

				$product_details_out = html_writer::end_tag('a');
				$product_details_out .= html_writer::start_div('cart_item_details');
					$product_details_out .= html_writer::start_tag('h4');
					$product_details_out .= $course_details->fullname;
					$product_details_out .= html_writer::end_tag('h4');
					$product_details_out .= html_writer::start_tag('h5');
					$product_details_out .= $course_details->catname;
					$product_details_out .= html_writer::end_tag('h5');
				$product_details_out .= html_writer::end_div();

				$product_price_out = html_writer::start_div('cart_item_price');
				$product_price_out .= $course_details->courseprice;
				$product_price_out .= html_writer::end_div();

				$product_total_price_out = html_writer::start_div('cart_item_price_total');
				$product_total_price_out .= $cart_price->price;
				$product_total_price_out .= html_writer::end_div();

				$product_remove_out = html_writer::start_div('cart_item_remove');
				$product_remove_out .= html_writer::start_tag('a', array('href' => 'javascript:void(0)', 'class' => 'product_remove', 'id' => 'product_remove_'.$get_my_cart->courseid, 'post-url' => $CFG->wwwroot.'/local/educart/shop/remove_cart.php', 'course-id' => $get_my_cart->courseid, 'user-id' => $USER->id));
				$product_remove_out .= "X";
				$product_remove_out .= html_writer::end_tag('a');
				$product_remove_out .= html_writer::end_div();
				$table->data[] = array($product_image_out, $product_details_out, $product_price_out, $product_total_price_out, $product_remove_out);
				
				$total_payble_amount += $course_details->courseprice;
				$total_item += 1;
			}
				
		}

		$out .= html_writer::table($table);
		$out .= html_writer::end_div();
		$out .= html_writer::end_div();
		$out .= html_writer::start_div('coupon_applied');


		
		$out .= html_writer::start_tag('form', array('method' => 'POST', 'action' => 'javascript:void(0)'));
		$out .= html_writer::start_tag('input', array('id' => 'coupon_valid_url', 'type' => 'hidden', 'value' => $CFG->wwwroot .'/local/educart/shop/is_valid_coupon_check.php'));
		$out .= html_writer::start_tag('input', array('id' => 'coupon_field', 'type' => 'text', 'name' => 'coupon_code'));
		$out .= html_writer::start_tag('input', array('id' => 'apply_coupon', 'userid' => $USER->id, 'type' => 'submit', 'class' => 'apply_coupon educart_button', 'value' => get_string('apply_coupon', 'local_educart')));
		$out .= html_writer::start_div('coupon_info', array('style' => 'display:none'));
		$out .= get_string('invalid_coupon', 'local_educart');
		$out .= html_writer::end_div();
		$out .= html_writer::start_div('coupon_info_success', array('style' => 'display:none'));
		$out .= get_string('couponapplysuccess', 'local_educart');
		$out .= html_writer::end_div();
		$out .= html_writer::end_tag('form', array());
		$out .= html_writer::end_div();

		$out .= html_writer::start_div('cart_bill');
		$cart_table = new html_table();
		$cart_table->head  = array();
		$cart_table->attributes['class'] = 'cart_total generaltable';
		$cart_table->data = array();
		//$total_payble_amount = $DB->get_record_sql("SELECT SUM( price ) as totalpayble FROM {educart_cart} WHERE userid = ? and status = ? and courseid = ?", array($USER->id, 0, ));
		$total_discount = $DB->get_record_sql("SELECT SUM( e.cost ) as total_regular_price FROM {enrol} AS e JOIN  {educart_cart} AS ec ON e.courseid = ec.courseid AND ec.userid = ? AND ec.status = ? AND e.enrol IN ('paypal')", array($USER->id, 0));

		$cart_table->data[] = array("Price <span>".$total_item." item(s)</span>", $total_payble_amount);
		$cart_table->data[] = array(get_string('total_discount', 'local_educart'), "");
		$cart_table->data[] = array(get_string('amount_payble', 'local_educart'), $total_payble_amount);

		$out .= html_writer::table($cart_table);
		$out .= html_writer::end_div();
		$out .= html_writer::start_div('clearfix');
		$out .= html_writer::end_div();
		$out .= html_writer::end_div();
		$out .= html_writer::start_div('cart_buttons');
		$out .= html_writer::start_tag('a', array('href' => $CFG->wwwroot.'/local/educart/shop/', 'class' => 'continue_shopping educart_button'));
		$out .= get_string('continue_shopping', 'local_educart');
		$out .= html_writer::end_tag('a');

		$out .= html_writer::start_tag('form', array('method' => 'POST', 'action' => $CFG->wwwroot .'/local/educart/shop/checkout.php'));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'cmd', 'value' => '_xclick'));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'charset', 'value' => 'utf-8'));
		//$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'business', 'value' => 'sandipamukherjee1990.pro@gmail.com'));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'currency_code', 'value' => 'USD'));
		//$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'amount', 'value' => $total_payble_amount->totalpayble ));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'for_auction', 'value' => false ));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'no_note', 'value' => 1 ));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'no_shipping', 'value' => 1 ));

		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'first_name', 'value' => $USER->firstname ));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'last_name', 'value' => $USER->lastname ));
		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'payer_email', 'value' => $USER->email ));

		$out .= html_writer::start_tag('input', array('id' => 'hidden_coupon_applied', 'type' => 'hidden', 'name' => 'coupon_name', 'value' => ''));

		$out .= html_writer::start_tag('input', array('type' => 'hidden', 'name' => 'courses', 'value' => json_encode($course_purchase)));
		$out .= html_writer::end_tag('input');
		$out .= html_writer::start_tag('input', array('type' => 'submit', 'class' => 'checkout', 'value' => get_string('checkoutpaypal', 'local_educart')));
		
		$out .= html_writer::end_tag('input');
		$out .= html_writer::end_tag('form', array());
		$out .= html_writer::end_div();
	}
echo $out;

$PAGE->requires->js('/local/educart/js/jquery.js');
$PAGE->requires->js('/local/educart/js/cart.js');
//$PAGE->requires->js('/local/educart/js/coupon.js');

echo $OUTPUT->footer();
echo '<script src="http://localhost/moodle35/local/educart/js/coupon.js"></script>';




