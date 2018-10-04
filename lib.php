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
 * Educart local plugin shop & cart page link configuration.
 *
 * @package    local_educart
 * @copyright  2018 Eruditiontec
 * @author     sandipa mukherjee <sandipamukherjee1990@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_educart_extend_navigation(global_navigation $navigation) {
    $nodemain = $navigation->add(get_string("pluginname", "local_educart"));
    $nodeshop = $navigation->add(get_string('shop', 'local_educart'), new moodle_url("/local/educart/shop"), navigation_node::TYPE_CUSTOM,
            null, null);
    $nodecart = $navigation->add(get_string('cart', 'local_educart'), new moodle_url("/local/educart/shop/cart.php"), navigation_node::TYPE_CUSTOM,
            null, null);
    $nodemain->showinflatnavigation = $nodeshop->showinflatnavigation = $nodecart->showinflatnavigation = true;
}

function local_educart_extend_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE;
    // Only add this settings item on non-site course pages.
    if (!$PAGE->course or $PAGE->course->id == 1) {
        return;
    }
 
    // Only let users with the appropriate capability see this settings item.
   
 
    /*if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
        $addcourseinshop = get_string('addcourseinshop', 'local_educart', $PAGE->course->fullname);
        $url = new moodle_url('/local/educart/settings/add_course_in_shop.php', array('id' => $PAGE->course->id));
        $node_add_course_in_shop = navigation_node::create(
            $addcourseinshop,
            $url,
            navigation_node::NODETYPE_LEAF,
            'educart',
            'educart',
            new pix_icon('t/addcontact', $addcourseinshop)
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $node_add_course_in_shop->make_active();
        }
        $settingnode->add_node($node_add_course_in_shop);
    }*/
}

function add_coupon($add_coupon) {
    GLOBAL $DB, $CFG;

    $add_coupon_obj = new stdClass();
    $add_coupon_obj->coupon_name = $add_coupon->coupon_name;
    if (isset($add_coupon->coupon_desc) && !empty($add_coupon->coupon_desc)) {
        $add_coupon_obj->coupon_desc = $add_coupon->coupon_desc;
    } else {
        $add_coupon_obj->coupon_desc = '';
    }
    $add_coupon_obj->status = 1;
    $add_coupon_obj->discount_type = $add_coupon->discount_type;
    if (isset($add_coupon->coupon_amount) && !empty($add_coupon->coupon_amount)) {
        $add_coupon_obj->coupon_amount = $add_coupon->coupon_amount;
    } else {
        $add_coupon_obj->coupon_amount = 0;
    }
    $add_coupon_obj->coupon_expiry = $add_coupon->coupon_expiry;
    if (isset($add_coupon->minimum_spend) && !empty($add_coupon->minimum_spend)) {
        $add_coupon_obj->minimum_spend = $add_coupon->minimum_spend;
    } else {
        $add_coupon_obj->minimum_spend = 0;
    }
    if (isset($add_coupon->maximum_spend) && !empty($add_coupon->maximum_spend)) {
        $add_coupon_obj->maximum_spend = $add_coupon->maximum_spend;
    } else {
        $add_coupon_obj->maximum_spend = 0;
    }
    if (isset($add_coupon->coupon_for_course) && !empty($add_coupon->coupon_for_course)) {
        $add_coupon_obj->coupon_for_course = implode(',', $add_coupon->coupon_for_course);
    } else {
        $add_coupon_obj->coupon_for_course = '-';
    }
    if (isset($add_coupon->coupon_for_exclude_course) && !empty($add_coupon->coupon_for_exclude_course)) {
        $add_coupon_obj->coupon_for_exclude_course = implode(',', $add_coupon->coupon_for_exclude_course);
    } else {
        $add_coupon_obj->coupon_for_exclude_course = '-';
    }
    if (isset($add_coupon->allowed_emails) && !empty($add_coupon->allowed_emails)) {
        $add_coupon_obj->allowed_emails = implode(',', $add_coupon->allowed_emails);
    } else {
        $add_coupon_obj->allowed_emails = '-';
    }
    if (isset($add_coupon->usage_limit_per_coupon) && !empty($add_coupon->usage_limit_per_coupon)) {
        $add_coupon_obj->usage_limit_per_coupon = $add_coupon->usage_limit_per_coupon;
    } else {
        $add_coupon_obj->usage_limit_per_coupon = '-';
    }
    if (isset($add_coupon->usage_limit_per_user) && !empty($add_coupon->usage_limit_per_user)) {
        $add_coupon_obj->usage_limit_per_user = $add_coupon->usage_limit_per_user;
    } else {
        $add_coupon_obj->usage_limit_per_user = '-';
    }
    if (isset($add_coupon->usage_limit_x_item) && !empty($add_coupon->usage_limit_x_item)) {
        $add_coupon_obj->usage_limit_x_item = $add_coupon->usage_limit_x_item;
    } else {
        $add_coupon_obj->usage_limit_x_item = '-';
    }
    $DB->insert_record('educart_coupons', $add_coupon_obj, false);
    redirect($CFG->wwwroot.'/local/educart/settings/coupons.php');
}
function update_coupon($update_coupon) {
    GLOBAL $DB, $CFG;
    $update_coupon_obj = new stdClass();
    $update_coupon_obj->id = $update_coupon->id;
    $update_coupon_obj->coupon_name = $update_coupon->coupon_name;
    $update_coupon_obj->coupon_desc = $update_coupon->coupon_desc;
    $update_coupon_obj->discount_type = $update_coupon->discount_type;
    $update_coupon_obj->coupon_amount = $update_coupon->coupon_amount;
    $update_coupon_obj->coupon_expiry = $update_coupon->coupon_expiry;
    $update_coupon_obj->minimum_spend = $update_coupon->minimum_spend;
    $update_coupon_obj->maximum_spend = $update_coupon->maximum_spend;
    if (isset($update_coupon->coupon_for_course) && !empty($update_coupon->coupon_for_course)) {
        $update_coupon_obj->coupon_for_course = implode(',', $update_coupon->coupon_for_course);
    } else {
        $update_coupon_obj->coupon_for_course = '-';
    }
    if (isset($update_coupon->coupon_for_exclude_course) && !empty($update_coupon->coupon_for_exclude_course)) {
        $update_coupon_obj->coupon_for_exclude_course = implode(',', $update_coupon->coupon_for_exclude_course);
    } else {
        $update_coupon_obj->coupon_for_exclude_course = '-';
    }
    if (isset($update_coupon->allowed_emails) && !empty($update_coupon->allowed_emails)) {
        $update_coupon_obj->allowed_emails = implode(',', $update_coupon->allowed_emails);
    } else {
        $update_coupon_obj->allowed_emails = '-';
    }
    $update_coupon_obj->usage_limit_per_coupon = $update_coupon->usage_limit_per_coupon;
    $update_coupon_obj->usage_limit_per_user = $update_coupon->usage_limit_per_user;
    $update_coupon_obj->usage_limit_x_item = $update_coupon->usage_limit_x_item;
    $DB->update_record('educart_coupons', $update_coupon_obj, false);
    redirect($CFG->wwwroot.'/local/educart/settings/coupons.php');
}
function delete_coupon($delete_coupon) {
    GLOBAL $DB, $CFG;
    $coupon_for_delete = $DB->get_record('educart_coupons', array('id' => $delete_coupon));
    $delete_coupon_obj = new stdClass();
    $delete_coupon_obj->id = $coupon_for_delete->id;
    $delete_coupon_obj->coupon_name = $coupon_for_delete->coupon_name;
    $delete_coupon_obj->coupon_desc = $coupon_for_delete->coupon_desc;
    $delete_coupon_obj->status = 0;
    $delete_coupon_obj->discount_type = $coupon_for_delete->discount_type;
    $delete_coupon_obj->coupon_amount = $coupon_for_delete->coupon_amount;
    $delete_coupon_obj->coupon_expiry = $coupon_for_delete->coupon_expiry;
    $delete_coupon_obj->minimum_spend = $coupon_for_delete->minimum_spend;
    $delete_coupon_obj->maximum_spend = $coupon_for_delete->maximum_spend;
    $delete_coupon_obj->coupon_for_course = $coupon_for_delete->coupon_for_course;
    $delete_coupon_obj->coupon_for_exclude_course = $coupon_for_delete->coupon_for_exclude_course;
    $delete_coupon_obj->allowed_emails = $coupon_for_delete->allowed_emails;
    $delete_coupon_obj->usage_limit_per_coupon = $coupon_for_delete->usage_limit_per_coupon;
    $delete_coupon_obj->usage_limit_per_user = $coupon_for_delete->usage_limit_per_user;
    $delete_coupon_obj->usage_limit_x_item = $coupon_for_delete->usage_limit_x_item;
    $DB->update_record('educart_coupons', $delete_coupon_obj, false);
    redirect($CFG->wwwroot.'/local/educart/settings/coupons.php');
}
