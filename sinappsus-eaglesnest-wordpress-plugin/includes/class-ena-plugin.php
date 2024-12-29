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

define('ENA_SINAPPSUS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ENA_SINAPPSUS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ENA_SINAPPSUS_API_URL', 'https://api-ena.sinappsus.us/api');

// Include the config file
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'config.php';

// Include admin menu and settings related functionality
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'admin/class-admin-menu.php';
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'settings/class-register-settings.php';


// Include CRM, API connector, and utility functions
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'utils/class-api-connector.php';


require_once ENA_SINAPPSUS_PLUGIN_DIR . 'crm/class-crm-handler.php';
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'crm/class-funnels-handler.php';


// Include shortcode functionality
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'shortcode/class-crm-shortcode.php';
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'shortcode/class-funnel-shortcode.php';
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'shortcode/class-reservations-shortcode.php';
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'shortcode/class-events-shortcode.php';


// Enqueue FontAwesome
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'assets/class-assets-handler.php';

// Include AJAX Handlers
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'includes/ajax-handlers/ajax-handlers.php';

// Include Elementor Plugin
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'elementor/class-elementor-config.php';


// Add custom fields to registration form by requiring the file
require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'core/register.php');