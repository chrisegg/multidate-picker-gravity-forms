<?php
/*
Plugin Name: Multi-Date Picker Field for Gravity Forms
Plugin URI: https://gravityranger.com/gravity-forms-multi-date-picker/
Description: Allows selection of multiple dates in a single Gravity Forms text field.
Version: 1.2
Author: Chris Eggleston
Author URI: https://gravityranger.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: multidate-picker-gravity-forms
*/

namespace GravityRanger\MultiDatePicker;

if (!defined('ABSPATH')) exit;

// Load translations
add_action('plugins_loaded', function() {
    load_plugin_textdomain('multidate-picker-gravity-forms', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

class Plugin {
    private $config;

    public function __construct() {
        $this->config = apply_filters('gf_multi_date_picker_config', [
            [
                'form_id' => absint(1),
                'field_id' => absint(6),
                'date_format' => sanitize_text_field('mm/dd/yy'),
            ],
        ]);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_filter('gform_field_validation', [$this, 'validate_field'], 10, 4);
    }

    public function enqueue_assets() {
        // Enqueue jQuery UI Datepicker
        wp_enqueue_script('jquery-ui-datepicker');

        // Enqueue CSS
        wp_enqueue_style(
            'gf-multi-date-picker',
            plugin_dir_url(__FILE__) . 'assets/css/multi-date-picker.css',
            [],
            '1.1'
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'gf-multi-date-picker',
            plugin_dir_url(__FILE__) . 'assets/js/multi-date-picker.js',
            ['jquery', 'jquery-ui-datepicker'],
            '1.1',
            true
        );

        // Pass config to JavaScript
        wp_localize_script(
            'gf-multi-date-picker',
            'gfMultiDatePickerConfig',
            $this->config
        );
    }

    public function validate_field($result, $value, $form, $field) {
        foreach ($this->config as $config) {
            if ($form['id'] == $config['form_id'] && $field->id == $config['field_id']) {
                if ($field->isRequired && (empty($value) || !is_string($value))) {
                    $result['is_valid'] = false;
                    $result['message'] = __('Please select at least one date.', 'multidate-picker-gravity-forms');
                }
            }
        }
        return $result;
    }
}

new Plugin();
