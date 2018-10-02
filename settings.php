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
}


/*$settings->add(new admin_setting_heading('local_educart_coupon_general', get_string('general', 'local_educart'), ''));
$name = 'local_educart/discount_type';
$title = get_string('discount_type', 'local_educart');
$description = get_string('discount_type_desc', 'local_educart');
$default = 'fixed_cart';
$setting = new admin_setting_configselect($name, $title, $description, $default, array(
    'percent' => get_string('percent', 'local_educart'),
    'fixed_cart' => get_string('fixed_cart', 'local_educart'),
    'fixed_product' => get_string('fixed_product', 'local_educart')
));
$setting->set_updatedcallback('theme_reset_all_caches');
$settings->add($setting);*/