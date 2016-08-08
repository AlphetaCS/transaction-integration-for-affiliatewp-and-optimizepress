<?php

add_action("admin_menu", "tiaop_custom_admin_menu");

function tiaop_custom_admin_menu() {
	add_options_page(
		"AffiliateWP + OptimizePress Integration Settings",
		"T.I. A+O",
		"manage_options",
		"tiaop-plugin",
		"tiaop_options_page"
	);

	add_action("admin_init", "tiaop_register_settings");
}

function tiaop_register_settings() {
	// Expiration
	register_setting("tiaop-settings", "tiaop_expiration_value");
	register_setting("tiaop-settings", "tiaop_expiration_units");

	// History
	register_setting("tiaop-settings", "tiaop_retain_history_value");
	register_setting("tiaop-settings", "tiaop_retain_history_units");
}

function tiaop_options_page() {
	require_once TIAOP_PLUGIN_DIR . "includes/configuration/tiaop_settings.php";
}

function tiaop_settings_plugin_link($links) {
	$settings_link = '<a href="options-general.php?page=tiaop-plugin">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

?>