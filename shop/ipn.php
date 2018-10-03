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
 * ipn page
 *
 * @package    enrol_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../../config.php');
require_once($CFG->dirroot."/enrol/paypal/lib.php");
require_once($CFG->libdir.'/eventslib.php');
require_once($CFG->libdir.'/enrollib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/local/educart/classes/util.php');
GLOBAL $DB;
// PayPal does not like when we return error messages here,
// the custom handler just logs exceptions and stops.
set_exception_handler(\local_educart\util::get_exception_handler());
if (!empty($_POST)) {//
	$myfile = fopen("response.log", "w") or die("Unable to open file!");
	fwrite($myfile, print_r($_POST, true));
	/*foreach ($_POST as $key => $value) {
	    $req .= "&$key=".urlencode($value);
	}*/
	// Required for message_send.
	$PAGE->set_context(context_system::instance());


	$isorder = $DB->get_record('educart_order', array('id' => $_POST['custom']));//
	fwrite($myfile, print_r($isorder, true));
	$data = new stdClass();
	$data->userid = $isorder->userid;
	$data->courseid = $isorder->courseids;
	/// get the user and course records
	if(!empty($isorder)) {
		if (! $user = $DB->get_record("user", array("id"=>$isorder->userid))) {//100 for test
		    \local_educart\util::message_transaction_error_to_admin("Not a valid user id", $data);
		    die;
		}
	
		foreach (explode(",", $isorder->courseids) as $coursetoenrol) {
			fwrite($myfile, print_r($coursetoenrol, true));
			$enrolinstance = $DB->get_record('enrol', array('enrol' => 'paypal', 'courseid' => $coursetoenrol, 'status' => 0));
			if (!empty($enrolinstance)) {
				$plugin = enrol_get_plugin('paypal');
				if ($enrolinstance->enrolperiod) {
		            $timestart = time();
		            $timeend   = $timestart + $enrolinstance->enrolperiod;
		        } else {
		            $timestart = 0;
		            $timeend   = 0;
		        }
		        // Enrol user
	        	$plugin->enrol_user($enrolinstance, $isorder->userid, $enrolinstance->roleid, $timestart, $timeend);
	        	
			    $course = $DB->get_record('course', array('id' => $coursetoenrol));
			    $user = $DB->get_record('user', array('id' => $isorder->userid));
			    $context = context_course::instance($coursetoenrol, IGNORE_MISSING);
			    // Pass $view=true to filter hidden caps if the user cannot see them
		        if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
		                                             '', '', '', '', false, true)) {
		            $users = sort_by_roleassignment_authority($users, $context);
		            $teacher = array_shift($users);
		        } else {
		            $teacher = false;
		        }
			    $mailstudents = $plugin->get_config('mailstudents');
        		$mailteachers = $plugin->get_config('mailteachers');
        		$mailadmins   = $plugin->get_config('mailadmins');
        		
        		$shortname = $course->shortname;
        		if (!empty($mailstudents)) {
        			$courselink = html_writer::link(new moodle_url('/course/view.php', array('id'=>$course->id)), $course->fullname);
        			$fields = array(
			        	'[[coursename]]',
			        	'[[firstname]]',
			        	'[[lastname]]',
			        	'[[coursefullname]]',
			            '[[courselink]]'
			        );
			        $values = array(
			            $course->fullname,
			            $user->firstname,
						$user->lastname,
						$course->fullname,
						$courselink
		        	);
		        	$tempsubject = str_replace($fields, $values, get_config('local_educart', 'config_student_email_subject'));
		        	$tempbody = str_replace($fields, $values, get_config('local_educart', 'config_student_email_body'));
		            $a = new stdClass();
		            $a->coursename = $course->fullname;
		            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$isorder->userid";

		            $eventdata = new \core\message\message();
		            $eventdata->courseid          = $coursetoenrol;
		            $eventdata->modulename        = 'moodle';
		            $eventdata->component         = 'local_educart';
		            $eventdata->name              = 'local_educart_transaction_enrolment';
		            $eventdata->userfrom          = empty($teacher) ? core_user::get_noreply_user() : $teacher;
		            $eventdata->userto            = $user;
		            $eventdata->subject           = $tempsubject;
		            $eventdata->fullmessage       = $tempbody;
		            $eventdata->fullmessageformat = FORMAT_MARKDOWN;
		            $eventdata->fullmessagehtml   = $tempbody;
		            $eventdata->smallmessage      = 'huphup';
		            fwrite($myfile, print_r('user:', true));
		            fwrite($myfile, print_r($user, true));
		            message_send($eventdata);
		            
		        }
		        if (!empty($mailteachers) && !empty($teacher)) {
		        	$fields = array(
			        	'[[coursename]]',
			        	'[[firstname]]',
			        	'[[lastname]]',
			        	'[[studentname]]'
			        );
			        $values = array(
			            $course->fullname,
			            $teacher->firstname,
						$teacher->lastname,
						$user->firstname.' '.$user->lastname
		        	);
		        	$tempsubject = str_replace($fields, $values, get_config('local_educart', 'config_teacher_email_subject'));
		        	$tempbody = str_replace($fields, $values, get_config('local_educart', 'config_teacher_email_body'));
		        	$a = new stdClass();
		            $a->course = $course->fullname;
		            $a->user = fullname($user);

		            $eventdata = new \core\message\message();
		            $eventdata->courseid          = $course->id;
		            $eventdata->modulename        = 'moodle';
		            $eventdata->component         = 'local_educart';
		            $eventdata->name              = 'local_educart_transaction_enrolment';
		            $eventdata->userfrom          = $user;
		            $eventdata->userto            = $teacher;
		            $eventdata->subject           = $tempsubject;
		            $eventdata->fullmessage       = $tempbody;
		            $eventdata->fullmessageformat = FORMAT_MARKDOWN;
		            $eventdata->fullmessagehtml   = $tempbody;
		            $eventdata->smallmessage      = 'oii';
		            fwrite($myfile, print_r('teacher:', true));
		            fwrite($myfile, print_r($teacher, true));
		            message_send($eventdata);  
		        }
		        if (!empty($mailadmins)) {
		        	$fields = array(
			        	'[[coursename]]',
			        	'[[firstname]]',
			        	'[[lastname]]',
			        	'[[studentname]]'
			        );
		            $a->course = $course->fullname;
		            $a->user = fullname($user);
		            $admins = get_admins();
		            foreach ($admins as $admin) {
		            	$values = array(
				            $course->fullname,
				            $admin->firstname,
							$admin->lastname,
							$user->firstname.' '.$user->lastname
		        		);
		        		$tempsubject = str_replace($fields, $values, get_config('local_educart', 'config_admin_email_subject'));
		        		$tempbody = str_replace($fields, $values, get_config('local_educart', 'config_admin_email_body'));
		                $eventdata = new \core\message\message();
		                $eventdata->courseid          = $course->id;
		                $eventdata->modulename        = 'moodle';
		                $eventdata->component         = 'local_educart';
		                $eventdata->name              = 'local_educart_transaction_enrolment';
		                $eventdata->userfrom          = $user;
		                $eventdata->userto            = $admin;
		                $eventdata->subject           = $tempsubject;
		                $eventdata->fullmessage       = $tempbody;
		                $eventdata->fullmessageformat = FORMAT_MARKDOWN;
		                $eventdata->fullmessagehtml   = $tempbody;
		                $eventdata->smallmessage      = 'ami admin';
		                fwrite($myfile, print_r('admin:', true));
		                fwrite($myfile, print_r($admin, true));
		                message_send($eventdata);
		            }
        		}
			} else {
				\local_educart\util::message_transaction_error_to_admin("Not a valid instance id", $data);
	    		die;
			}
		}
		$orderupdate = new stdClass();
		$orderupdate->id = $isorder->id;
		$orderupdate->userid = $isorder->userid;
	    $orderupdate->courseids = $isorder->courseids;
	    $orderupdate->timecreated = time();
	    if ($_POST['payment_status'] == 'Completed') {//
	    	$orderupdate->status = "Completed";/*default*/
	    }
	    $DB->update_record('educart_order', $orderupdate, false);
	    fwrite($myfile, print_r($orderupdate, true));
	}
}
fclose($myfile);