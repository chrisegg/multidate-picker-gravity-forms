<?php
namespace GravityRanger\MultiDatePicker;

if (!defined('ABSPATH')) {
    exit;
}

class GFMultiDatePickerSettings extends \GFAddOn {
    protected $_version = '1.3';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'gf-multi-date-picker-settings';
    protected $_path = 'multidate-picker-gravity-forms/multidate-picker-gravity-forms.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Multi-Date Picker Settings';
    protected $_short_title = 'Multi-Date Settings';

    private static $_instance = null;

    public static function get_instance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function plugin_settings_fields() {
        return [
            [
                'title' => __('Multi-Date Picker Configurations', 'multidate-picker-gravity-forms'),
                'fields' => [
                    [
                        'name' => 'configs',
                        'type' => 'repeater',
                        'label' => __('Date Field Configurations', 'multidate-picker-gravity-forms'),
                        'add_button_text' => __('Add Configuration', 'multidate-picker-gravity-forms'),
                        'fields' => [
                            [
                                'name' => 'form_id',
                                'label' => __('Form ID', 'multidate-picker-gravity-forms'),
                                'type' => 'text',
                                'required' => true,
                                'class' => 'small',
                                'sanitize_callback' => 'absint',
                            ],
                            [
                                'name' => 'field_id',
                                'label' => __('Field ID', 'multidate-picker-gravity-forms'),
                                'type' => 'text',
                                'required' => true,
                                'class' => 'small',
                                'sanitize_callback' => 'absint',
                            ],
                            [
                                'name' => 'date_format',
                                'label' => __('Date Format', 'multidate-picker-gravity-forms'),
                                'type' => 'text',
                                'default_value' => 'mm/dd/yy',
                                'class' => 'medium',
                                'sanitize_callback' => 'sanitize_text_field',
                            ],
                            [
                                'name' => 'output_format',
                                'label' => __('Output Format', 'multidate-picker-gravity-forms'),
                                'type' => 'select',
                                'choices' => [
                                    [
                                        'label' => __('Comma-separated', 'multidate-picker-gravity-forms'),
                                        'value' => 'comma',
                                    ],
                                    [
                                        'label' => __('JSON', 'multidate-picker-gravity-forms'),
                                        'value' => 'json',
                                    ],
                                ],
                                'default_value' => 'comma',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
