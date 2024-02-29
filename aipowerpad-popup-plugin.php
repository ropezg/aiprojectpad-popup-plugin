<?php
/*
Plugin Name: AIPowerPad Popup Plugin
Plugin URI: https://aiprojectpad.com/aiprojectpad-popup-plugin
Description: Displays a customizable popup with user-defined content.
Version: 1.0
Author: Petar R
Author URI: https://aiprojectpad.com
*/

if (!defined('ABSPATH')) {
    exit;
}

function aipowerpad_sanitize_popup_content($input) {
    return $input; // Implement appropriate sanitization.
}

class AIPowerPad_Popup_Plugin {
    private $default_settings = [
        'enable_popup' => '0', // Disabled by default
        'delay_time' => 5,
        'popup_content' => 'This is your default popup content. Customize it in the plugin settings.',
        'popup_width' => 300,
        'popup_height' => 200,
        'auto_hide' => '',
        'max_show' => ''
    ];

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_menu', [$this, 'add_plugin_page']);
        add_action('admin_init', [$this, 'page_init']);
    }

public function enqueue_scripts() {
    // Enqueue the CSS for the popup
    wp_enqueue_style('aipowerpad-popup-css', plugin_dir_url(__FILE__) . 'css/aipowerpad-popup.css', [], '1.0.0');

    // Enqueue the JavaScript for the popup
    wp_enqueue_script('aipowerpad-popup-js', plugin_dir_url(__FILE__) . 'js/aipowerpad-popup.js', ['jquery'], '1.0.0', true);

    // Enqueue the js-cookie library for handling cookies
    wp_enqueue_script('js-cookie', 'https://cdn.jsdelivr.net/npm/js-cookie@3/dist/js.cookie.min.js', [], '3.0.0', true);

    // Localize script for passing PHP variables to JS
    wp_localize_script('aipowerpad-popup-js', 'popup_params', [
        'enablePopup' => get_option('aipowerpad_popup_enable', $this->default_settings['enable_popup']),
        'delayTime' => get_option('aipowerpad_popup_delay_time', $this->default_settings['delay_time']),
        'popupContent' => get_option('aipowerpad_popup_content', $this->default_settings['popup_content']),
        'popupWidth' => get_option('aipowerpad_popup_width', $this->default_settings['popup_width']),
        'popupHeight' => get_option('aipowerpad_popup_height', $this->default_settings['popup_height']),
        'autoHide' => get_option('aipowerpad_popup_auto_hide', $this->default_settings['auto_hide']),
        'maxShow' => get_option('aipowerpad_popup_max_show', $this->default_settings['max_show'])
    ]);
}


    public function add_plugin_page() {
        add_options_page('AIPowerPad Popup Settings', 'AIPowerPad Popup', 'manage_options', 'aipowerpad-popup', [$this, 'create_admin_page']);
    }

    public function create_admin_page() {
        require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';
    }

    public function page_init() {
        register_setting('aipowerpad_popup_option_group', 'aipowerpad_popup_enable', ['sanitize_callback' => 'absint']);
        register_setting('aipowerpad_popup_option_group', 'aipowerpad_popup_delay_time', ['sanitize_callback' => 'absint']);
        register_setting('aipowerpad_popup_option_group', 'aipowerpad_popup_content', ['sanitize_callback' => 'aipowerpad_sanitize_popup_content']);
        register_setting('aipowerpad_popup_option_group', 'aipowerpad_popup_width', ['sanitize_callback' => 'absint']);
        register_setting('aipowerpad_popup_option_group', 'aipowerpad_popup_height', ['sanitize_callback' => 'absint']);
        register_setting('aipowerpad_popup_option_group', 'aipowerpad_popup_auto_hide', ['sanitize_callback' => 'absint']);
        register_setting('aipowerpad_popup_option_group', 'aipowerpad_popup_max_show', ['sanitize_callback' => 'absint']);

        add_settings_section('setting_section_id', 'AIPowerPad Popup Settings', null, 'aipowerpad-popup-admin');

        $this->add_settings_fields();
    }

    private function add_settings_fields() {
        add_settings_field('enable_popup', 'Enable Popup', [$this, 'enable_popup_callback'], 'aipowerpad-popup-admin', 'setting_section_id');
        add_settings_field('delay_time', 'Delay Time (seconds)', [$this, 'delay_time_callback'], 'aipowerpad-popup-admin', 'setting_section_id');
        add_settings_field('popup_content', 'Popup Content (HTML allowed)', [$this, 'popup_content_callback'], 'aipowerpad-popup-admin', 'setting_section_id');
        add_settings_field('popup_width', 'Popup Width (pixels)', [$this, 'popup_width_callback'], 'aipowerpad-popup-admin', 'setting_section_id');
        add_settings_field('popup_height', 'Popup Height (pixels)', [$this, 'popup_height_callback'], 'aipowerpad-popup-admin', 'setting_section_id');
        add_settings_field('auto_hide', 'Auto-Hide Time (seconds)', [$this, 'auto_hide_callback'], 'aipowerpad-popup-admin', 'setting_section_id');
        add_settings_field('max_show', 'Maximum Appearances', [$this, 'max_show_callback'], 'aipowerpad-popup-admin', 'setting_section_id');
    }

    public function enable_popup_callback() {
        $value = get_option('aipowerpad_popup_enable', $this->default_settings['enable_popup']);
        printf('<input type="checkbox" id="enable_popup" name="aipowerpad_popup_enable" value="1" %s /> Enable', checked(1, $value, false));
    }

    // Callback functions for other settings fields here...
// Callback function for 'delay_time'
public function delay_time_callback() {
    $value = get_option('aipowerpad_popup_delay_time', $this->default_settings['delay_time']);
    printf('<input type="text" id="delay_time" name="aipowerpad_popup_delay_time" value="%s" />', esc_attr($value));
}

// Callback function for 'popup_content'
public function popup_content_callback() {
    $value = get_option('aipowerpad_popup_content', $this->default_settings['popup_content']);
    wp_editor($value, 'aipowerpad_popup_content', ['textarea_name' => 'aipowerpad_popup_content']);
}

// Callback function for 'popup_width'
public function popup_width_callback() {
    $value = get_option('aipowerpad_popup_width', $this->default_settings['popup_width']);
    printf('<input type="text" id="popup_width" name="aipowerpad_popup_width" value="%s" />', esc_attr($value));
}

public function popup_height_callback() {
    $value = get_option('aipowerpad_popup_height');
    $value = ($value === false) ? $this->default_settings['popup_height'] : $value; // Use default if not set
    printf('<input type="number" id="popup_height" name="aipowerpad_popup_height" value="%s" min="0" style="width: 100px;" />', esc_attr($value));
}

public function auto_hide_callback() {
    $value = get_option('aipowerpad_popup_auto_hide');
    $value = is_numeric($value) ? $value : ''; // Keep blank if not set or not numeric
    printf('<input type="number" id="auto_hide" name="aipowerpad_popup_auto_hide" value="%s" min="0" style="width: 100px;" /><p class="description">Set the time in seconds after which the popup will automatically close. Leave blank to disable auto-hide.</p>', esc_attr($value));
}

public function max_show_callback() {
    $value = get_option('aipowerpad_popup_max_show');
    $value = is_numeric($value) ? $value : ''; // Keep blank if not set or not numeric
    printf('<input type="number" id="max_show" name="aipowerpad_popup_max_show" value="%s" min="0" style="width: 100px;" /><p class="description">Set how many times the popup will appear to a user. Leave blank for unlimited appearances.</p>', esc_attr($value));
}

}

$aipowerpad_popup_plugin = new AIPowerPad_Popup_Plugin();

function aipowerpad_popup_add_content() {
    if (!is_admin() && get_option('aipowerpad_popup_enable', '0') == '1') { // Ensure the popup is enabled
        $delayTime = get_option('aipowerpad_popup_delay_time', '5') * 1000; // Convert seconds to milliseconds for JavaScript
        $popupContent = get_option('aipowerpad_popup_content', 'This is your default popup content. Customize it in the plugin settings.');
        $popupWidth = get_option('aipowerpad_popup_width', '300');
        $popupHeight = get_option('aipowerpad_popup_height', '200');
        $autoHide = get_option('aipowerpad_popup_auto_hide', '');
        $autoHideTime = is_numeric($autoHide) && $autoHide > 0 ? intval($autoHide) * 1000 : 'false'; // Convert seconds to milliseconds or set to false
        $maxShow = intval(get_option('aipowerpad_popup_max_show', '0'));

        // Echo the HTML structure for the popup
        echo "<div id='aipowerpad-popup-overlay' style='display:none;'></div>"; // The overlay
        echo "<div id='aipowerpad-popup-container' style='display:none; width: {$popupWidth}px; height: {$popupHeight}px;'>"; // The popup container
        echo "<span class='aipowerpad-popup-dismiss'>X</span>"; // The dismiss button
        echo "<div class='aipowerpad-popup-content'>{$popupContent}</div>"; // The content area
        echo "</div>";

        // Now, echo the script to handle the timing, display logic, and maximum appearances
        echo "<script type='text/javascript'>
                jQuery(document).ready(function($) {
                    var maxShows = {$maxShow};
                    var shownCount = Cookies.get('aipowerpad_popup_shown') ? parseInt(Cookies.get('aipowerpad_popup_shown')) : 0;

                    if (maxShows === 0 || shownCount < maxShows) {
                        setTimeout(function() {
                            $('#aipowerpad-popup-overlay').fadeIn();
                            $('#aipowerpad-popup-container').fadeIn();
                            Cookies.set('aipowerpad_popup_shown', shownCount + 1, { expires: 365 });
                        }, {$delayTime});
                    }

                    $('#aipowerpad-popup-overlay, .aipowerpad-popup-dismiss').click(function() {
                        $('#aipowerpad-popup-container').fadeOut();
                        $('#aipowerpad-popup-overlay').fadeOut();
                    });

                    " . ($autoHideTime !== 'false' ? "setTimeout(function() {
                        $('#aipowerpad-popup-container').fadeOut();
                        $('#aipowerpad-popup-overlay').fadeOut();
                    }, {$delayTime} + {$autoHideTime});" : "") . "
                });
              </script>";
    }
}
add_action('wp_footer', 'aipowerpad_popup_add_content');

