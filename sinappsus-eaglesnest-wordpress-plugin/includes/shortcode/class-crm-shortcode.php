<?php 


// Shortcode for form
add_shortcode('ena_sinappsus_form', 'ena_sinappsus_form_shortcode');
function ena_sinappsus_form_shortcode($atts) {
    $atts = shortcode_atts(['funnel_id' => ''], $atts);
    ob_start();

    if (empty($atts['funnel_id'])) {
        echo '<p>Please provide a Sales Funnel ID.</p>';
        return ob_get_clean();
    }

    $funnel_data = get_funnel_data($atts['funnel_id']);
    if (empty($funnel_data)) {
        echo '<p>Invalid Sales Funnel ID.</p>';
        return ob_get_clean();
    }

    $crm_tunnel_id = $funnel_data['crm_tunnel_id'];
    $user_persona_id = $funnel_data['user_persona_id'];

    $fields = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'profile_picture' => 'Profile Picture',
        'dob' => 'Date of Birth',
        'address.street_line_01' => 'Street Line 01',
        'address.street_line_02' => 'Street Line 02',
        'address.street_line_03' => 'Street Line 03',
        'address.city' => 'City',
        'address.state' => 'State',
        'address.zip' => 'Zip',
        'address.country' => 'Country',
    ];

    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ena_sinappsus_form_nonce', 'ena_sinappsus_nonce'); ?>
        <input type="hidden" name="funnel_id" value="<?php echo esc_attr($atts['funnel_id']); ?>">
        <input type="hidden" name="crm_tunnel_id" value="<?php echo esc_attr($crm_tunnel_id); ?>">
        <input type="hidden" name="user_persona_id" value="<?php echo esc_attr($user_persona_id); ?>">

        <?php foreach ($fields as $field => $label) : ?>
            <label for="<?php echo esc_attr($field); ?>"><?php echo esc_html($label); ?>:</label>
            <input type="text" id="<?php echo esc_attr($field); ?>" name="<?php echo esc_attr($field); ?>">
        <?php endforeach; ?>

        <input type="submit" name="ena_sinappsus_submit" value="Submit">
    </form>
    <?php

    if (isset($_POST['ena_sinappsus_submit']) && check_admin_referer('ena_sinappsus_form_nonce', 'ena_sinappsus_nonce')) {
        $data = [
            'funnel_id' => sanitize_text_field($_POST['funnel_id']),
            'crm_tunnel_id' => sanitize_text_field($_POST['crm_tunnel_id']),
            'user_persona_id' => sanitize_text_field($_POST['user_persona_id']),
        ];

        foreach ($fields as $field => $label) {
            $data[$field] = sanitize_text_field($_POST[$field]);
        }

        $response = ena_sinappsus_connect_to_api('/contacts', $data, 'POST');
        if ($response && isset($response['id']) && $response['id']) {
            echo '<p>Form submitted successfully!</p>';
        } else {
            echo '<p>Failed to submit form.</p>';
        }
    }

    return ob_get_clean();
}
