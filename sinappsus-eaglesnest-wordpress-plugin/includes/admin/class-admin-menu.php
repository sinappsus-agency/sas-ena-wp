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
        <?php display_users_table(); ?>
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

// Function to display users in an editable table form
function display_users_table() {
    $users = ena_sinappsus_get_contacts();
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo esc_html($user['id']); ?></td>
                    <td><?php echo esc_html($user['name']); ?></td>
                    <td><?php echo esc_html($user['email']); ?></td>
                    <td>
                        <button class="edit-user" data-id="<?php echo esc_attr($user['id']); ?>">Edit</button>
                        <button class="delete-user" data-id="<?php echo esc_attr($user['id']); ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

// Function to handle adding, editing, and deleting users
function handle_user_crud_operations() {
    if (isset($_POST['action'])) {
        $action = sanitize_text_field($_POST['action']);
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $user_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
        );

        switch ($action) {
            case 'add':
                ena_sinappsus_add_lead($user_data);
                break;
            case 'edit':
                ena_sinappsus_update_user($user_id, $user_data);
                break;
            case 'delete':
                ena_sinappsus_delete_user($user_id);
                break;
        }
    }
}
add_action('admin_post_handle_user_crud_operations', 'handle_user_crud_operations');
