# Multi-Date Picker Field for Gravity Forms

Contributors: chrisegg <br>
Tags: gravity forms, datepicker, multi-date <br>
Requires at least: 5.0 <br>
Tested up to: 6.4 <br>
Stable tag: 1.2 <br>
License: GPLv2 or later <br>
License URI: https://www.gnu.org/licenses/gpl-2.0.html

## Description
This plugin enhances Gravity Forms by adding a multi-date picker to Date fields, allowing users to select multiple dates in a single field. Configure settings via the WordPress admin.

## Installation
1. Upload the `multidate-picker-gravity-forms` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Add a Date field to your Gravity Forms form.
4. Go to Settings > Multi-Date Picker to configure form and field IDs, date format, and output format.

## Changelog
 = 1.3 =
* Added support for Gravity Forms Date fields.
* Improved integration with Gravity Forms rendering and validation.
* Adopted Gravity Forms Add-On Framework for better integration.
* Added settings page for configuring multiple Date fields.
* Supported custom output formats (comma-separated or JSON).
* Added filter for customizing datepicker appearance and behavior.
* Separated core and settings logic into dedicated class files.

 = 1.2 =
* Improved performance and maintainability with separate CSS/JS files.
* Added accessibility attributes.
* Enhanced compatibility with WordPress standards.
