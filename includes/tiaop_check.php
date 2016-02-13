<?php

include_once ABSPATH . "wp-admin/includes/plugin.php";

function tiaop_check_plugins() {
	// Check for AffiliateWP
	$missing_awp = !is_plugin_active("affiliate-wp/affiliate-wp.php");

	// Check for OptimizeMember
	$missing_omp = !is_plugin_active("optimizeMember/optimizeMember.php");

	// Display message
	if($missing_awp)
		add_action("admin_notices", "tiaop_missing_awp");
	if($missing_omp)
		add_action("admin_notices", "tiaop_missing_omp");

	// Results
	$check_passed = !$missing_awp && !$missing_omp;

	return $check_passed;
}

function tiaop_missing_awp() {
	echo '<div class="notice error"><p>Transaction Integration for AffiliateWP and OptimizePress: AffiliateWP plugin not found</p></div>';
}

function tiaop_missing_omp() {
	echo '<div class="notice error"><p>Transaction Integration for AffiliateWP and OptimizePress: OptimizeMember plugin not found</p></div>';
}

?>