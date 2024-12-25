<?php

// Register settings
add_action('admin_init', 'ena_sinappsus_register_settings');
function ena_sinappsus_register_settings() {
    register_setting('ena_sinappsus_settings_group', 'ena_sinappsus_username');
    register_setting('ena_sinappsus_settings_group', 'ena_sinappsus_password');
    register_setting('ena_sinappsus_settings_group', 'ena_sinappsus_jwt_token');
    register_setting('ena_sinappsus_settings_group', 'ena_sinappsus_refresh_token');
    register_setting('ena_sinappsus_settings_group', 'ena_sinappsus_token_expiry');

    add_settings_section('ena_sinappsus_settings_section', 'API Authentication', null, 'ena_sinappsus_settings');

    add_settings_field('ena_sinappsus_username', 'Username', 'ena_sinappsus_username_callback', 'ena_sinappsus_settings', 'ena_sinappsus_settings_section');
    add_settings_field('ena_sinappsus_password', 'Password', 'ena_sinappsus_password_callback', 'ena_sinappsus_settings', 'ena_sinappsus_settings_section');
    add_settings_field('ena_sinappsus_jwt_token', 'JWT Token', 'ena_sinappsus_jwt_token_callback', 'ena_sinappsus_settings', 'ena_sinappsus_settings_section');
    add_settings_field('ena_sinappsus_refresh_token', 'Refresh Token', 'ena_sinappsus_refresh_token_callback', 'ena_sinappsus_settings', 'ena_sinappsus_settings_section');
    add_settings_field('ena_sinappsus_token_expiry', 'Token Expiry', 'ena_sinappsus_token_expiry_callback', 'ena_sinappsus_settings', 'ena_sinappsus_settings_section');
}

function ena_sinappsus_username_callback() {
    $username = get_option('ena_sinappsus_username');
    echo '<input type="text" id="ena_sinappsus_username" name="ena_sinappsus_username" value="' . esc_attr($username) . '" />';
}

function ena_sinappsus_password_callback() {
    $password = get_option('ena_sinappsus_password');
    echo '<input type="password" id="ena_sinappsus_password" name="ena_sinappsus_password" value="' . esc_attr($password) . '" />';
}

function ena_sinappsus_jwt_token_callback() {
    $jwt_token = get_option('ena_sinappsus_jwt_token');
    echo '<input type="text" id="ena_sinappsus_jwt_token" name="ena_sinappsus_jwt_token" value="' . esc_attr($jwt_token) . '" readonly />';
}

function ena_sinappsus_refresh_token_callback() {
    $refresh_token = get_option('ena_sinappsus_refresh_token');
    echo '<input type="text" id="ena_sinappsus_refresh_token" name="ena_sinappsus_refresh_token" value="' . esc_attr($refresh_token) . '" readonly />';
}

function ena_sinappsus_token_expiry_callback() {
    $token_expiry = get_option('ena_sinappsus_token_expiry');
    echo '<input type="text" id="ena_sinappsus_token_expiry" name="ena_sinappsus_token_expiry" value="' . esc_attr($token_expiry) . '" readonly />';
}

// Handle AJAX request to save settings
add_action('wp_ajax_save_jwt_settings', 'save_jwt_settings');
function save_jwt_settings() {
    update_option('ena_sinappsus_username', sanitize_text_field($_POST['ena_sinappsus_username']));
    update_option('ena_sinappsus_password', sanitize_text_field($_POST['ena_sinappsus_password']));
    update_option('ena_sinappsus_jwt_token', sanitize_text_field($_POST['ena_sinappsus_jwt_token']));
    update_option('ena_sinappsus_refresh_token', sanitize_text_field($_POST['ena_sinappsus_refresh_token']));
    update_option('ena_sinappsus_token_expiry', sanitize_text_field($_POST['ena_sinappsus_token_expiry']));
    wp_send_json_success();
}
