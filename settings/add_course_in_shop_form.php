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
 * Form for add course in shop with price, tax, discount
 *
 * @package    local_educart
 * @copyright  2017 sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');

class course_price_form extends moodleform {

	public function get_currencies() {
        // See https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside,
        // 3-character ISO-4217: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_currency_codes
        $codes = array(
            'AUD', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'JPY',
            'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RUB', 'SEK', 'SGD', 'THB', 'TRY', 'TWD', 'USD');
        $currencies = array();
        foreach ($codes as $c) {
            $currencies[$c] = new lang_string($c, 'core_currencies');
        }

        return $currencies;
    }
	public function definition() {
		GLOBAL $DB;

		$mform = $this->_form;
		$courseid = $this->_customdata['courseid'];
		$course_price_data = $DB->get_record('educart_course_price', array('courseid' => $courseid));
		$mform->addElement('hidden', 'courseid', $courseid);
		$mform->setType('courseid', PARAM_INT);

		$mform->addElement('text', 'price', get_string('regular_price', 'local_educart'));
		$mform->setType('price', PARAM_FLOAT);
		$mform->addRule('price', get_string('numeric_error', 'local_educart'), 'numeric', null, 'client');
		$mform->addRule('price', get_string('required'), 'required', null, 'client');

		/*$paypalcurrencies = $this->get_currencies();

        $mform->addElement('select', 'currency', get_string('currency', 'local_educart'), $paypalcurrencies);
        $mform->setDefault('currency', 'USD');*/

		$mform->addElement('text', 'tax', get_string('tax', 'local_educart'));
		$mform->setType('tax', PARAM_FLOAT);
		$mform->addRule('tax', get_string('numeric_error', 'local_educart'), 'numeric', null, 'client');

		$mform->addElement('text', 'discount', get_string('discount', 'local_educart'));
		$mform->setType('discount', PARAM_FLOAT);
		$mform->addRule('discount', get_string('numeric_error', 'local_educart'), 'numeric', null, 'client');

		$this->set_data($course_price_data);
		$this->add_action_buttons();
		
	}
}