<?php
/*
 * Plugin Name: ENA: SINAPPSUS WordPress Plugin
 * Description: the official ENA wordpress plugin for integration into the ENA HEADLESS API
 * Plugin URI: https://eaglesnestatitlan.com
 * Author URI: https://eaglesnestatitlan.com
 * Version: 0.0.1
 * Author: Sinappsus
 * Requires at least: 5.0
 * Tested up to: 6.0
*/

/**
 * Main plugin file
 */

defined('ABSPATH') || exit;

define('ENA_SINAPPSUS_VERSION', '1.0.11');
define('ENA_SINAPPSUS_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));
define('ENA_SINAPPSUS_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

// Plugin update checker
require_once __DIR__ . '/plugin-update/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/sinappsus-agency/sas-ena-wp/raw/main/sinappsus-eaglesnest-wordpress-plugin/info.json',  // The URL of the metadata file.
	__FILE__, // Full path to the main plugin file.
	'ena-sinappsus-plugin'  // Plugin slug. Usually it's the same as the name of the directory.
);

function ena_sinappsus_plugin()
{
	require_once(plugin_basename('includes/class-ena-plugin.php'));
	load_plugin_textdomain('ena-sinappsus-plugin', false, trailingslashit(dirname(plugin_basename(__FILE__))));
}

add_action('plugins_loaded', 'ena_sinappsus_plugin', 0);

// Plugin links
function ena_plugin_links($links)
{
	$settings_url = add_query_arg(
		array(
			'page' => 'ena-settings',
			'tab' => 'crm',
			'section' => 'ena_sinappsus_plugin',
		),
		admin_url('admin.php')
	);

	$plugin_links = array(
		'<a href="' . esc_url($settings_url) . '">' . __('Settings', 'ena-sinappsus-plugin') . '</a>',
		'<a href="#">' . __('Support', 'ena-sinappsus-plugin') . '</a>',
		'<a href="#">' . __('Docs', 'ena-sinappsus-plugin') . '</a>',
	);

	return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ena_plugin_links');
