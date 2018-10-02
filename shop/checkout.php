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
 * Educart enrolment plugin checkout.
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_login();
$PAGE->set_url('/local/educart/shop/checkout.php');
GLOBAL $CFG, $USER, $SITE;
include_once("config.php");

$paypalurl = ($PayPalMode=='sandbox') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){
    
	$querystring = '';
	// Firstly Append paypal account to querystring
    $querystring .= "?business=".urlencode($PayPalbussinessemail)."&";
	$courses_purchase = implode(',', json_decode($_POST['courses'], true));
	$courses_in_querystring = implode('-', json_decode($_POST['courses'], true));
	
	$total_payble_amount = $DB->get_record_sql("SELECT SUM( price ) as totalpayble FROM {educart_cart} WHERE userid = ? AND STATUS = ? AND courseid IN (".$courses_purchase.")", array($USER->id, 0));
    //$courses_cart_updates = json_decode($_POST['courses']);
    
	$querystring .= "amount=".urlencode($total_payble_amount->totalpayble - $_POST['coupon_name'])."&";
    //$querystring .= "quantity=".urlencode(count($courses_cart_updates))."&";
	//loop for posted values and append to querystring

    foreach($_POST as $key => $value){
    	if($key != "courses") {
    		$value = urlencode(stripslashes($value));
        	$querystring .= "$key=$value&";
    	} else {
            $item_count = 1;
            foreach (json_decode($value) as $courseid) {
                $coursename = $DB->get_record("course", array("id" => $courseid));
                $querystring .= "item_number_".$item_count."=".$courseid;
                $querystring .= "&item_name_".$item_count."=".$coursename->fullname."&";
                $item_count += 1;
            }
        }
        
    }

    
    // Redirect to paypal IPN

    $isorder = $DB->get_record('educart_order', array('courseids' => $courses_purchase, 'status' => 'ordered'));

    if (empty($isorder)) {
        $orderplace = new stdClass();
        $orderplace->userid = $USER->id;
        $orderplace->courseids = $courses_purchase;
        $orderplace->timecreated = time();
        $orderplace->status = "ordered";/*default*/
        $lastinsertorderid = $DB->insert_record('educart_order', $orderplace, false);
        $customid = $lastinsertorderid;
    } else {
        $orderplace = new stdClass();
        $orderplace->id = $isorder->id;
        $orderplace->userid = $USER->id;
        $orderplace->courseids = $courses_purchase;
        $orderplace->timecreated = time();
        $orderplace->status = "ordered";/*default*/
        $lastinsertorderid = $DB->update_record('educart_order', $orderplace, false);
        $customid = $isorder->id;
    }
    
    // Append querystring with custom field
    $querystring .= "custom=".$customid;
    // Append paypal return addresses
    $querystring .= "&item_name=".$SITE->fullname;
    $querystring .= "&return=".urlencode(stripslashes($PayPalReturnURL))."?custom=".$customid."&";
    $querystring .= "cancel_return=".urlencode(stripslashes($PayPalCancelURL))."&";
    $querystring .= "notify_url=".urlencode($PayPalNotifyURL);
print_r($querystring);
    header('location:'.$paypalurl.$querystring);
    exit();
}