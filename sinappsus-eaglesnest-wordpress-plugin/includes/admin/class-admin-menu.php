<?php 

// Add admin menu and submenus
add_action('admin_menu', 'ena_sinappsus_add_admin_menu');
function ena_sinappsus_add_admin_menu() {
    error_log('ena_sinappsus_connect_to_api() has been called');
    
    add_menu_page('ENA SYSTEM', 'ENA SYSTEM', 'manage_options', 'ena-system', 'ena_sinappsus_settings_page', 'dashicons-admin-generic');
    add_submenu_page('ena-system', 'CRM', 'CRM', 'manage_options', 'ena-crm', 'ena_sinappsus_crm_page');
    add_submenu_page('ena-system', 'Reservations', 'Reservations', 'manage_options', 'ena-reservations', 'ena_sinappsus_reservations_page');
    add_submenu_page('ena-system', 'Funnel Steps', 'Funnel Steps', 'manage_options', 'funnel-steps', 'funnel_steps_page_html');
}

// Enqueue the JavaScript file
add_action('admin_enqueue_scripts', 'enqueue_jwt_auth_scripts');

function enqueue_jwt_auth_scripts($hook) {
    // Only load the script on the specific admin page
    if ($hook !== 'toplevel_page_ena-system') {
        return;
    }

    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Enqueue the custom script
    wp_enqueue_script('jwt-auth-script', plugin_dir_url(__FILE__) . '../../includes/scripts/authentication.js', array('jquery'), null, true);

    // Localize the script with the AJAX URL
    wp_localize_script('jwt-auth-script', 'jwtAuth', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}

// Settings page
function ena_sinappsus_settings_page() {
    ?>
    <div class="wrap">
        <h1>ENA SYSTEM Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ena_sinappsus_settings_group');
            do_settings_sections('ena_sinappsus_settings');
            // wp_nonce_field('ena_sinappsus_settings_nonce', 'ena_sinappsus_nonce');
            ?>
            <p class="submit">
                <button type="button" id="authenticate-button" class="button button-primary">Authenticate</button>
                <button type="button" id="validate-button" class="button">Validate</button>
                <?php submit_button(); ?>
            </p>
            <p id="timer"></p>
            <p id="message"></p>
        </form>
    </div>
    <?php
}

// CRM page
function ena_sinappsus_crm_page() {
    ?>
    <div class="wrap">
        <h1>CRM</h1>
        <p>Here you can manage your CRM contacts and leads.</p>
        <!-- Add your CRM management code here -->
    </div>
    <?php
}

// Reservations page
function ena_sinappsus_reservations_page() {
    ?>
    <div class="wrap">
        <h1>Reservations</h1>
        <p>Here you can manage your reservations.</p>
        <!-- Add your reservations management code here -->
    </div>
    <?php
}


// Admin page HTML to link pages and funnel steps
function funnel_steps_page_html() {
    // Fetch all published pages
    $pages = get_pages();

    // Fetch the sales funnel steps (You need to replace this with your actual steps)
    $funnel_steps = array('Step 1', 'Step 2', 'Step 3');

    ?>
    <div class="wrap">
        <h1>Link Funnel Steps to Pages</h1>
        <form method="post" action="options.php">
            <?php settings_fields('funnel_step_options'); ?>
            <?php do_settings_sections('funnel_step_options'); ?>
            <?php wp_nonce_field('funnel_steps_nonce', 'funnel_steps_nonce'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Page</th>
                    <td>
                        <select name="funnel_step_page">
                            <?php foreach ($pages as $page) { ?>
                                <option value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Select Funnel Step</th>
                    <td>
                        <select name="funnel_step_name">
                            <?php foreach ($funnel_steps as $step) { ?>
                                <option value="<?php echo esc_attr($step); ?>"><?php echo esc_html($step); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
