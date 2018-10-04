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
 * Form for add coupons
 *
 * @package    local_educart
 * @copyright  2017 sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');

class coupon_form extends moodleform {
	
	public function definition() {
		GLOBAL $DB;
		$mform = $this->_form;
		$mform->addElement('header','general', get_string('general', 'local_educart'));
		
		$mform->addElement('text', 'coupon_name', get_string('coupon_name', 'local_educart'));
		$mform->setType('coupon_name', PARAM_TEXT);
		$mform->addRule('coupon_name', get_string('missing', 'local_educart'), 'required', null, 'server');
		$mform->addElement('textarea', 'coupon_desc', get_string("coupon_desc", "local_educart"), 'wrap="virtual" rows="10" cols="50"');
		$discount_type_options = array(
		    'percent' => get_string('percent', 'local_educart'),
		    'fixed_cart' => get_string('fixed_cart', 'local_educart'),
		    'fixed_product' => get_string('fixed_product', 'local_educart')
		);
		$select = $mform->addElement('select', 'discount_type', get_string('discount_type', 'local_educart'), $discount_type_options);
		$select->setSelected('fixed_cart');

		$mform->addElement('text', 'coupon_amount', get_string('coupon_amount', 'local_educart'));
		$mform->addHelpButton('coupon_amount', 'coupon_amount', 'local_educart');
		$mform->setType('coupon_amount', PARAM_FLOAT);
		$mform->setDefault('coupon_amount', 0);
		//$mform->addRule('coupon_amount', get_string('missing', 'local_educart'), 'required', null, 'server');
		$mform->addElement('date_selector', 'coupon_expiry', get_string('expiry_date', 'local_educart'));

		$mform->addElement('header','usage_restriction', get_string('usage_restriction', 'local_educart'));

		$mform->addElement('text', 'minimum_spend', get_string('minimum_spend', 'local_educart'));
		$mform->addHelpButton('minimum_spend', 'minimum_spend', 'local_educart');
		$mform->setType('minimum_spend', PARAM_FLOAT);
		$mform->setDefault('minimum_spend', 0);

		$mform->addElement('text', 'maximum_spend', get_string('maximum_spend', 'local_educart'));
		$mform->addHelpButton('maximum_spend', 'maximum_spend', 'local_educart');
		$mform->setType('maximum_spend', PARAM_FLOAT);
		$mform->setDefault('maximum_spend', 0);
		
		$courses_for_shops = $DB->get_records_sql("Select c.id, c.shortname from {course} as c 
		LEFT JOIN {course_categories} as cc ON c.category = cc.id 
		JOIN {enrol} as e ON c.id = e.courseid and e.enrol = ? and e.status = ? and c.visible = ?", array("paypal", 0, 1));
		
		$coupon_for_course = array();
		foreach ($courses_for_shops as $course_for_shop) {
			$coupon_for_course[$course_for_shop->id] = $course_for_shop->shortname;
		}
		
		$options = array(        
            'multiple' => true,             
            'noselectionstring' => get_string('search_course', 'local_educart'),                                                                
        );         
        $mform->addElement('autocomplete', 'coupon_for_course', get_string('coupon_for_course', 'local_educart'), $coupon_for_course, $options);

        $mform->addElement('autocomplete', 'coupon_for_exclude_course', get_string('coupon_for_exclude_course', 'local_educart'), $coupon_for_course, $options);

        $user_lists = $DB->get_records_sql('SELECT * FROM {user} where confirmed = ? and deleted = ? and suspended = ? and id != ?', array(1, 0, 0, 1));
        $allowed_user = array();
        foreach ($user_lists as $user_list) {
        	$allowed_user[$user_list->id] = $user_list->email;
        }
        $options = array(        
            'multiple' => true,             
            'noselectionstring' => 'Search allowed users'                          
        );  
        $mform->addElement('autocomplete', 'allowed_emails', get_string('allowed_emails', 'local_educart'), $allowed_user, $options);

        $mform->addElement('header','usage_limits', get_string('usage_limits', 'local_educart'));
        $mform->addElement('text', 'usage_limit_per_coupon', get_string('usage_limit_per_coupon', 'local_educart'));
        $mform->setType('usage_limit_per_coupon', PARAM_INT);
        $mform->addElement('text', 'usage_limit_per_user', get_string('usage_limit_per_user', 'local_educart'));
        $mform->setType('usage_limit_per_user', PARAM_INT);
        $mform->addElement('html', '<div class="hide">');
        $mform->addElement('text', 'usage_limit_x_item', get_string('usage_limit_x_item', 'local_educart'));
        $mform->setDefault('usage_limit_x_item', 0);
        $mform->setType('usage_limit_x_item', PARAM_INT);
        $mform->addElement('html', '<div>');

        $couponid = $this->_customdata['couponid'];
        if(!empty($couponid)) {
        	$coupon_data = $DB->get_record('educart_coupons', array('id' => $couponid));
			$mform->addElement('hidden', 'id', $couponid);
			$mform->setType('id', PARAM_INT);
        }
		
		$this->add_action_buttons();
		
	}
	/**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    function validation($data, $files) {
    	GLOBAL $DB;
    	$errors = parent::validation($data, $files);
    	// Add field validation check for duplicate couponname.
        if ($coupon = $DB->get_record('educart_coupons', array('coupon_name' => $data['coupon_name']), '*', IGNORE_MULTIPLE)) {
            if (empty($data['id']) || $coupon->id != $data['id']) {
                $errors['coupon_name'] = get_string('coupon_nametaken', 'local_educart', $coupon->coupon_name);
            }
        }
    	if (empty($data['coupon_amount']) || $data['coupon_amount'] < 0) {
    		$errors['coupon_amount'] = get_string('coupon_amount_err', 'local_educart');
    	}
    	if(!empty($data['coupon_for_course']) && !empty($data['coupon_for_exclude_course'])) {
			if(!empty(array_intersect($data['coupon_for_course'], $data['coupon_for_exclude_course']))){
	    		$errors['coupon_for_exclude_course'] = get_string('same_element', 'local_educart');
	    		$errors['coupon_for_course'] = get_string('same_element', 'local_educart');
	    	}
    	} else if (empty($data['coupon_for_course']) && !empty($data['coupon_for_exclude_course'])) {
			$errors['coupon_for_course'] = get_string('must_choose', 'local_educart');
    	} else if (!empty($data['coupon_for_course']) && empty($data['coupon_for_exclude_course'])) {
			$errors['coupon_for_exclude_course'] = get_string('must_choose', 'local_educart');
    	}
    	

    	return $errors;
    }
}


