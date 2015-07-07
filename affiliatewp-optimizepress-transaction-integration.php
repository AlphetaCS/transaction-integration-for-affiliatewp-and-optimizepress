<?php
/**
 * Plugin Name: AffiliateWP - OptimizePress Transaction Integration
 * Plugin URI: http://github.com/AlphetaCS/affiliatewp-optimizepress-transaction-integration
 * Description: Integrates AffiliateWP with OptimizePress transactions
 * Author: Brandon Kessell
 * Author URI: http://www.alphetacs.com
 * Version: 1.0
 */

// Define constants
if (!defined('OPAFTI_PLUGIN_DIR' ) ) {
	define( 'OPAFTI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// With the given affiliate id, add the referral
function opafti_add_referral($affiliateId, $amount = "", $description = "", $reference = "", $context = "") {
	if( empty( $affiliateId ) ) {
		if(WP_DEBUG === true) {
			error_log("opafti_add_referral: affiliate_id not given");
		}
		return false;
	}

	// Build the array that affwp_add_referral expects
	$data = array(
		'affiliate_id' => $affiliateId,
		'amount' => $amount,
		'description' => $description,
		'reference' => $reference,
		'context' => $context,
		'status' => 'pending',
	);

	// Tell the log that we are about to call affwp_add_referral and what the data array contains
	if(WP_DEBUG === true) {
		error_log("opafti_add_referral: Calling referral function affwp_add_referral");
		error_log(print_r($data, true));
	}

	// Give the referral information to AffiliateWP
	affwp_add_referral($data);
}

// Include paypal
require_once OPAFTI_PLUGIN_DIR . 'includes/opafti_paypal_notify.php';