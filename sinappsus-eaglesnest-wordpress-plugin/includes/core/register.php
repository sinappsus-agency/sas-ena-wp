<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add settings field in admin area
add_action('admin_init', function() {
    add_settings_section('custom_registration_settings', 'Custom Registration Settings', null, 'general');
    add_settings_field('enable_custom_registration', 'Enable Custom Registration Fields', 'render_custom_registration_field', 'general', 'custom_registration_settings');
    register_setting('general', 'enable_custom_registration', 'boolval');
});

function render_custom_registration_field() {
    $value = get_option('enable_custom_registration', false);
    echo '<input type="checkbox" id="enable_custom_registration" name="enable_custom_registration" value="1"' . checked(1, $value, false) . '/>';
}

// Add custom fields to registration form
add_action('register_form', function() {
    if (get_option('enable_custom_registration')) {
        ?>
        <p>
            <label for="custom_field_1"><?php _e('Custom Field 1') ?><br/>
            <input type="text" name="custom_field_1" id="custom_field_1" class="input" value="<?php echo esc_attr(wp_unslash($_POST['custom_field_1'] ?? '')); ?>" size="25" /></label>
        </p>
        <p>
            <label for="custom_field_2"><?php _e('Custom Field 2') ?><br/>
            <input type="text" name="custom_field_2" id="custom_field_2" class="input" value="<?php echo esc_attr(wp_unslash($_POST['custom_field_2'] ?? '')); ?>" size="25" /></label>
        </p>
        <?php
    }
});

// Validate and save custom fields
add_action('register_post', function($sanitized_user_login, $user_email, $errors) {
    if (get_option('enable_custom_registration')) {
        if (empty($_POST['custom_field_1'])) {
            $errors->add('custom_field_1_error', __('<strong>ERROR</strong>: Custom Field 1 is required.'));
        }
        if (empty($_POST['custom_field_2'])) {
            $errors->add('custom_field_2_error', __('<strong>ERROR</strong>: Custom Field 2 is required.'));
        }
    }
}, 10, 3);

add_action('user_register', function($user_id) {
    if (get_option('enable_custom_registration')) {
        update_user_meta($user_id, 'custom_field_1', sanitize_text_field($_POST['custom_field_1']));
        update_user_meta($user_id, 'custom_field_2', sanitize_text_field($_POST['custom_field_2']));

        // Submit to API
        $response = wp_remote_post(ENA_SINAPPSUS_API_URL . '/contacts', [
            'body' => json_encode([
                'user_id' => $user_id,
                'custom_field_1' => sanitize_text_field($_POST['custom_field_1']),
                'custom_field_2' => sanitize_text_field($_POST['custom_field_2']),
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }
});