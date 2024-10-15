<?php


// Register settings
add_action('admin_init', 'ena_sinappsus_register_settings');
function ena_sinappsus_register_settings() {
    register_setting('ena_sinappsus_settings_group', 'ena_sinappsus_account_key');
    add_settings_section('ena_sinappsus_settings_section', 'API Settings', null, 'ena_sinappsus_settings');
    add_settings_field('ena_sinappsus_account_key', 'Account Key', 'ena_sinappsus_account_key_callback', 'ena_sinappsus_settings', 'ena_sinappsus_settings_section');
}

function ena_sinappsus_account_key_callback() {
    $account_key = get_option('ena_sinappsus_account_key');
    echo '<input type="text" name="ena_sinappsus_account_key" value="' . esc_attr($account_key) . '" />';
}