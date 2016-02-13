<?php
/**
 * Plugin Name: Transaction Integration for AffiliateWP and OptimizePress
 * Plugin URI: http://github.com/AlphetaCS/affiliatewp-optimizepress-transaction-integration
 * Description: Integrates AffiliateWP with OptimizePress transactions
 * Author: Brandon Kessell
 * Author URI: http://www.alphetacs.com
 * Version: 2.0
 */

// Define constants
if (!defined("TIAOP_PLUGIN_DIR"))
	define("TIAOP_PLUGIN_DIR", plugin_dir_path(__FILE__));

if (!defined("TIAOP_PLUGIN_NAME"))
	define("TIAOP_PLUGIN_NAME", plugin_basename(__FILE__));

// Check for missing dependencies
require_once TIAOP_PLUGIN_DIR . "includes/tiaop_check.php";
$check_results = tiaop_check_plugins();

// Include database functions
require_once TIAOP_PLUGIN_DIR . "includes/tiaop_db.php";

// Create tables
tiaop_create_db_tables();

// Include processing functions
require_once TIAOP_PLUGIN_DIR . "includes/tiaop_processing.php";

// Add settings menu item and plugin page link
require_once TIAOP_PLUGIN_DIR . "includes/configuration/tiaop_build.php";
add_filter("plugin_action_links_" . TIAOP_PLUGIN_NAME, "tiaop_settings_plugin_link");

// Include paypal
require_once TIAOP_PLUGIN_DIR . "includes/tiaop_paypal_notify.php";

// Try to process saved purchases
if($check_results)
	add_action("wp_head", "tiaop_process_saved_purchases");
?>