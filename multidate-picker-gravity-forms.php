<?php
/*
Plugin Name: Multi-Date Picker Field for Gravity Forms
Plugin URI: https://gravityranger.com/gravity-forms-multi-date-picker/
Description: Adds multi-date selection to Gravity Forms Date fields.
Version: 1.3
Author: Chris Eggleston
Author URI: https://gravityranger.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: multidate-picker-gravity-forms
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load translations
add_action('plugins_loaded', function() {
    load_plugin_textdomain('multidate-picker-gravity-forms', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Register the add-on with Gravity Forms
add_action('gform_loaded', function() {
    if (!class_exists('GFForms')) {
        return;
    }

    // Include the Gravity Forms Add-On Framework
    if (!class_exists('GFAddOn')) {
        require_once(GFCommon::get_base_path() . '/includes/addon/class-gf-addon.php');
    }

    // Include core and settings classes
    require_once(plugin_dir_path(__FILE__) . 'includes/class-gf-multi-date-picker.php');
    require_once(plugin_dir_path(__FILE__) . 'includes/class-gf-multi-date-picker-settings.php');

    // Initialize the add-on
    GFAddOn::register('GravityRanger\MultiDatePicker\GFMultiDatePicker');
}, 5);
