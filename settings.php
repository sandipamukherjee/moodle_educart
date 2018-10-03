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
 * Educart local plugin settings.
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$settings = null;
if (is_siteadmin()) {
	
	$ADMIN->add('root', new admin_category('local_educart', get_string('pluginname', 'local_educart')));
	/* coupon Settings */

    $settings = new admin_externalpage(
            'local_educart_coupon',
            get_string('settings_coupon', 'local_educart'),
            new moodle_url('/local/educart/settings/coupons.php')
    );
    
    $ADMIN->add('local_educart', $settings);

    $settings = new admin_settingpage('local_educart_email', get_string('local_educart_email', 'local_educart'));
    $ADMIN->add('local_educart', $settings);

    $name = 'local_educart/config_student_email_subject';
    $default = get_string('student_email_subject', 'local_educart');
    $title = get_string('config_student_email_subject', 'local_educart');
    $description = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'local_educart/config_student_email_body';
    $default = get_string('student_email_body', 'local_educart');
    $title = get_string('config_student_email_body', 'local_educart');
    $description = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'local_educart/config_teacher_email_subject';
    $default = get_string('teacher_admin_email_subject', 'local_educart');
    $title = get_string('config_teacher_email_subject', 'local_educart');
    $description = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'local_educart/config_teacher_email_body';
    $default = get_string('teacher_admin_email_body', 'local_educart');
    $title = get_string('config_teacher_email_body', 'local_educart');
    $description = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'local_educart/config_admin_email_subject';
    $default = get_string('teacher_admin_email_subject', 'local_educart');
    $title = get_string('config_admin_email_subject', 'local_educart');
    $description = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'local_educart/config_admin_email_body';
    $default = get_string('teacher_admin_email_body', 'local_educart');
    $title = get_string('config_admin_email_body', 'local_educart');
    $description = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);
}