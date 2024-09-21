<?php
/*
Plugin Name: ENA SINAPPSUS
Description: A plugin for ENA SINAPPSUS that sets up a sidebar link with submenus for CRM and Reservations, and includes an admin page for settings.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

// Define constants
define('ENA_SINAPPSUS_VERSION', '1.0');
define('ENA_SINAPPSUS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ENA_SINAPPSUS_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include the config file
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'config.php';

// Add admin menu and submenus
add_action('admin_menu', 'ena_sinappsus_add_admin_menu');
function ena_sinappsus_add_admin_menu() {
	add_menu_page('ENA SYSTEM', 'ENA SYSTEM', 'manage_options', 'ena-system', 'ena_sinappsus_settings_page', 'dashicons-admin-generic');
	add_submenu_page('ena-system', 'CRM', 'CRM', 'manage_options', 'ena-crm', 'ena_sinappsus_crm_page');
	add_submenu_page('ena-system', 'Reservations', 'Reservations', 'manage_options', 'ena-reservations', 'ena_sinappsus_reservations_page');
	add_submenu_page('ena-system', 'Funnel Steps','Funnel Steps','manage_options','funnel-steps','funnel_steps_page_html');
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
			submit_button();
			?>
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

// Connect to the API
function ena_sinappsus_connect_to_api($endpoint, $data = array(), $method = 'GET') {
	$account_key = get_option('ena_sinappsus_account_key');
	$url = ENA_SINAPPSUS_API_URL . $endpoint;

	$args = array(
		'method' => $method,
		'headers' => array(
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $account_key,
		),
		'body' => json_encode($data),
	);

	$response = wp_remote_request($url, $args);
	return json_decode(wp_remote_retrieve_body($response), true);
}

// CRM functions
function ena_sinappsus_get_contacts() {
	return ena_sinappsus_connect_to_api('/crm/contacts');
}

function ena_sinappsus_add_lead($lead_data) {
	return ena_sinappsus_connect_to_api('/crm/leads', $lead_data, 'POST');
}

// Shortcode for form
add_shortcode('ena_sinappsus_form', 'ena_sinappsus_form_shortcode');
function ena_sinappsus_form_shortcode() {
	ob_start();
	?>
	<form method="post" action="">
		<label for="name">Name:</label>
		<input type="text" id="name" name="name" required>
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" required>
		<input type="submit" name="ena_sinappsus_submit" value="Submit">
	</form>
	<?php
	if (isset($_POST['ena_sinappsus_submit'])) {
		$lead_data = array(
			'name' => sanitize_text_field($_POST['name']),
			'email' => sanitize_email($_POST['email']),
		);
		$response = ena_sinappsus_add_lead($lead_data);
		if ($response && isset($response['success']) && $response['success']) {
			echo '<p>Lead added successfully!</p>';
		} else {
			echo '<p>Failed to add lead.</p>';
		}
	}
	return ob_get_clean();
}


// Function to read URL parameters and make API call for the sales funnel page
function handle_sales_funnel_step() {
    // Get step from URL parameter
    if (isset($_GET['step'])) {
        $step = sanitize_text_field($_GET['step']); // Ensure safe parameter handling

        // Prepare API URL based on the step
        $api_url = "https://your-api-url.com/steps/" . $step;

        // Make the API call
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            return 'API request failed';
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Check if the API returns HTML content or a page ID
        if (isset($data['html'])) {
            // Return the HTML content
            return $data['html'];
        } elseif (isset($data['next_page_id'])) {
            // Get the next page ID and redirect
            $next_page = get_permalink($data['next_page_id']);
            wp_redirect($next_page);
            exit;
        }
    }

    return 'No step parameter found.';
}

// Shortcode to load the sales funnel step
add_shortcode('load_funnel_step', 'handle_sales_funnel_step');

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

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Page</th>
                    <td>
                        <select name="funnel_step_page">
                            <?php foreach ($pages as $page) { ?>
                                <option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Select Funnel Step</th>
                    <td>
                        <select name="funnel_step_name">
                            <?php foreach ($funnel_steps as $step) { ?>
                                <option value="<?php echo $step; ?>"><?php echo $step; ?></option>
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

// Register settings for saving funnel steps to pages
function register_funnel_settings() {
    register_setting('funnel_step_options', 'funnel_step_page');
    register_setting('funnel_step_options', 'funnel_step_name');
}
add_action('admin_init', 'register_funnel_settings');
