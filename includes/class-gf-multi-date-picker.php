<?php
namespace GravityRanger\MultiDatePicker;

if (!defined('ABSPATH')) {
    exit;
}

class GFMultiDatePicker extends \GFAddOn {
    protected $_version = '1.3';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'gf-multi-date-picker';
    protected $_path = 'multidate-picker-gravity-forms/multidate-picker-gravity-forms.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Multi-Date Picker';
    protected $_short_title = 'Multi-Date Picker';

    private static $_instance = null;

    public static function get_instance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init() {
        parent::init();

        // Enqueue frontend assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Modify Date field output
        add_filter('gform_field_content', [$this, 'modify_date_field_output'], 10, 5);

        // Validate Date fields
        add_filter('gform_field_validation', [$this, 'validate_field'], 10, 4);
    }

    public function enqueue_assets() {
        // Enqueue jQuery UI Datepicker
        wp_enqueue_script('jquery-ui-datepicker');

        // Enqueue CSS
        wp_enqueue_style(
            'gf-multi-date-picker',
            plugin_dir_url(__FILE__) . '../assets/css/multi-date-picker.css',
            [],
            $this->_version
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'gf-multi-date-picker',
            plugin_dir_url(__FILE__) . '../assets/js/multi-date-picker.js',
            ['jquery', 'jquery-ui-datepicker'],
            $this->_version,
            true
        );

        // Get configs from settings
        $configs = get_option('gf_multi_date_picker_configs', [
            [
                'form_id' => absint(1),
                'field_id' => absint(6),
                'date_format' => sanitize_text_field('mm/dd/yy'),
                'output_format' => 'comma',
            ],
        ]);

        // Apply filter for customizations
        $configs = apply_filters('gf_multi_date_picker_config', $configs);

        // Pass config to JavaScript
        wp_localize_script(
            'gf-multi-date-picker',
            'gfMultiDatePickerConfig',
            $configs
        );

        // Allow custom datepicker options via filter
        $datepicker_options = apply_filters('gf_multi_date_picker_options', []);
        wp_localize_script(
            'gf-multi-date-picker',
            'gfMultiDatePickerOptions',
            $datepicker_options
        );
    }

    public function modify_date_field_output($content, $field, $value, $lead_id, $form_id) {
        $configs = get_option('gf_multi_date_picker_configs', []);
        foreach ($configs as $config) {
            if ($form_id == $config['form_id'] && $field->id == $config['field_id'] && $field->type === 'date') {
                // Prepare value based on output format
                $display_value = $value;
                if ($config['output_format'] === 'json' && !empty($value)) {
                    $dates = json_decode($value, true);
                    $display_value = is_array($dates) ? implode(', ', $dates) : $value;
                }

                // Replace the default datepicker input
                $input_id = "input_{$form_id}_{$field->id}";
                $content = preg_replace(
                    '/<input[^>]+id=[\'"]' . $input_id . '[\'"][^>]*>/',
                    '<div class="gf-multi-date-field"><input type="text" name="input_' . $field->id . '" id="' . $input_id . '" value="' . esc_attr($display_value) . '" class="datepicker gfield_date_multi" aria-describedby="datepicker-instructions" aria-label="Select multiple dates" /><span class="calendar-icon" role="button" aria-label="Open date picker"></span></div>',
                    $content
                );

                // Add hidden field for raw value
                $content .= '<input type="hidden" id="' . $input_id . '_hidden" name="' . $input_id . '_hidden" value="' . esc_attr($value) . '">';
            }
        }
        return $content;
    }

    public function validate_field($result, $value, $form, $field) {
        $configs = get_option('gf_multi_date_picker_configs', []);
        foreach ($configs as $config) {
            if ($form['id'] == $config['form_id'] && $field->id == $config['field_id'] && $field->type === 'date') {
                $parsed_value = $value;
                if ($config['output_format'] === 'json' && !empty($value)) {
                    $parsed_value = json_decode($value, true);
                    $parsed_value = is_array($parsed_value) ? implode(', ', $parsed_value) : $value;
                }
                if ($field->isRequired && (empty($parsed_value) || !is_string($parsed_value))) {
                    $result['is_valid'] = false;
                    $result['message'] = __('Please select at least one date.', 'multidate-picker-gravity-forms');
                }
            }
        }
        return $result;
    }
}
