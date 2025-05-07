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
                'form_id' => absint(1), // Your Gravity Forms form ID
                'field_id' => absint(6), // Your Gravity Forms Date field ID
                'date_format' => sanitize_text_field('mm/dd/yy'),
            ],
        ]);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_filter('gform_field_validation', [$this, 'validate_field'], 10, 4);
        add_filter('gform_field_content', [$this, 'modify_date_field_output'], 10, 5);
    }

    public function enqueue_assets() {
        // Enqueue jQuery UI Datepicker
        wp_enqueue_script('jquery-ui-datepicker');

        // Enqueue CSS
        wp_enqueue_style(
            'gf-multi-date-picker',
            plugin_dir_url(__FILE__) . 'assets/css/multi-date-picker.css',
            [],
            '1.2'
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'gf-multi-date-picker',
            plugin_dir_url(__FILE__) . 'assets/js/multi-date-picker.js',
            ['jquery', 'jquery-ui-datepicker'],
            '1.2',
            true
        );

        // Pass config to JavaScript
        wp_localize_script(
            'gf-multi-date-picker',
            'gfMultiDatePickerConfig',
            $this->config
        );
    }

    public function modify_date_field_output($content, $field, $value, $lead_id, $form_id) {
        // Target Date fields in the config
        foreach ($this->config as $config) {
            if ($form_id == $config['form_id'] && $field->id == $config['field_id'] && $field->type === 'date') {
                // Replace the default datepicker input with a custom one
                $input_id = "input_{$form_id}_{$field->id}";
                $content = preg_replace(
                    '/<input[^>]+id=[\'"]' . $input_id . '[\'"][^>]*>/',
                    '<div class="gf-multi-date-field"><input type="text" name="input_' . $field->id . '" id="' . $input_id . '" value="' . esc_attr($value) . '" class="datepicker gfield_date_multi" aria-describedby="datepicker-instructions" aria-label="Select multiple dates" /><span class="calendar-icon" role="button" aria-label="Open date picker"></span></div>',
                    $content
                );
            }
        }
        return $content;
    }

    public function validate_field($result, $value, $form, $field) {
        foreach ($this->config as $config) {
            if ($form['id'] == $config['form_id'] && $field->id == $config['field_id'] && $field->type === 'date') {
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
