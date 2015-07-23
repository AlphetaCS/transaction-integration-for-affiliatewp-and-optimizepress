<?php
/**
 * Plugin Name: AffiliateWP - OptimizePress Transaction Integration
 * Plugin URI: http://affiliatewp.com
 * Description: Integrates AffiliateWP with OptimizePress transactions
 * Author: Brandon Kessell
 * Author URI: http://www.alphetacs.com
 * Version: 1.0
 */

// Define constants
if (!defined('OPAFTI_PLUGIN_DIR' ) ) {
	define( 'OPAFTI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Include database functions
require_once OPAFTI_PLUGIN_DIR . 'includes/opafti_db.php';

// Create tables
opafti_create_db_tables();

// With the given affiliate id, add the referral
function opafti_add_referral($affiliateId, $amount = "", $description = "", $reference = "", $context = "", $visitId) {
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
		'visit_id' => $visitId,
		'status' => 'pending',
	);

	// Tell the log that we are about to call affwp_add_referral and what the data array contains
	if(WP_DEBUG === true) {
		error_log("opafti_add_referral: Calling referral function affwp_add_referral");
		error_log(print_r($data, true));
	}

	// Give the referral information to AffiliateWP
	$referralId = affiliate_wp()->referrals->add( array('affiliate_id' => $affiliateId, 'amount' => $amount, 'status' => 'pending', 'description' => $description, 'reference' => $reference, 'visit_id' => $visitId, 'context' => $context) );

	// Update the visit info
	affiliate_wp()->visits->update( $visitId, array( 'referral_id' => $referralId ), '', 'visit' );
}

function opafti_process_saved_purchases() {
	// Get the referral information
	$affiliateId = affiliate_wp()->tracking->get_affiliate_id();
	if( empty($affiliateId) ) {
		return;
	}

	// Check the database for saved purchases
	$savedPurchase = opafti_get_saved_purchase();
	if(!$savedPurchase)
		return;

	// Tell the log that we found a saved purchase and are attempting to process it
	if(WP_DEBUG === true) {
		error_log("opafti_add_referral: Found a saved purchase; attempting to process it");
		error_log(print_r($savedPurchase, true));
	}

	$amount = $savedPurchase[2];
	$description = $savedPurchase[3];
	$reference = $savedPurchase[4];

	// Get the visit id
	$visitId = affiliate_wp()->tracking->get_visit_id();

	// Call the function that adds the referral
	opafti_add_referral($affiliateId, $amount, $description, $reference, "", $visitId);

	// Delete this entry now that it was processed
	opafti_delete_current_ip();
}

// Include paypal
require_once OPAFTI_PLUGIN_DIR . 'includes/opafti_paypal_notify.php';

// Try to process saved purchases
add_action( 'wp_head', 'opafti_process_saved_purchases' );