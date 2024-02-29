<?php
// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Admin page markup
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
        <?php
        // Output security fields for the registered setting "aiprojectpad_popup_option_group"
        settings_fields('aiprojectpad_popup_option_group');
        // Output setting sections and their fields
        do_settings_sections('aiprojectpad-popup-admin');
        // Output save settings button
        submit_button('Save Settings');
        ?>
    </form>
</div>
