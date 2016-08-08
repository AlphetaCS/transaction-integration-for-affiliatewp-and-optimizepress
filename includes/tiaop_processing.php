<?php

// With the given affiliate id, add the referral
function tiaop_add_referral($affiliateId, $amount = "", $description = "", $reference = "", $context = "", $visitId, $is_test) {
	if( empty( $affiliateId)) {
		if(WP_DEBUG === true) {
			error_log("tiaop_add_referral: affiliate_id not given");
		}
		return false;
	}

	// Build the array that affwp_add_referral expects
	$data = array(
		"affiliate_id" => $affiliateId,
		"amount" => $amount,
		"description" => $description,
		"reference" => $reference,
		"context" => $context,
		"visit_id" => $visitId,
		"status" => "pending",
	);

	// Tell the log that we are about to call affwp_add_referral and what the data array contains
	if(WP_DEBUG === true) {
		error_log("tiaop_add_referral: Calling referral function affwp_add_referral");
		error_log(print_r($data, true));
	}

	// Give the referral information to AffiliateWP
	if(!$is_test)
		$referralId = affiliate_wp()->referrals->add( array("affiliate_id" => $affiliateId, "amount" => $amount, "status" => "pending", "description" => $description, "reference" => $reference, "visit_id" => $visitId, "context" => $context));

	// Update the visit info
	if(!$is_test)
		affiliate_wp()->visits->update($visitId, array( "referral_id" => $referralId), "", "visit");
}

function tiaop_process_saved_purchases($is_test = false) {
	// Get the referral information
	if($is_test)
		$affiliateId = 1;
	else
		$affiliateId = affiliate_wp()->tracking->get_affiliate_id();

	if(empty($affiliateId))
		return;

	// Check the database for saved purchases
	$savedPurchase = tiaop_get_saved_purchase();
	if(!$savedPurchase)
		return;

	// Tell the log that we found a saved purchase and are attempting to process it
	if(WP_DEBUG === true) {
		error_log("tiaop_add_referral: Found a saved purchase; attempting to process it");
		error_log(print_r($savedPurchase, true));
	}

	$amount = $savedPurchase[2];
	$description = $savedPurchase[3];
	$reference = $savedPurchase[4];
	$expiration = $savedPurchase[5];

	// Get the visit id
	if($is_test)
		$visitId = 2;
	else
		$visitId = affiliate_wp()->tracking->get_visit_id();

	// Call the function that adds the referral
	tiaop_add_referral($affiliateId, $amount, $description, $reference, "", $visitId, $is_test);

	// Log the link
	tiaop_add_history(tiaop_get_user_ip(), $amount, $description, $reference, $expiration, "Visit ID " . $visitId . " linked to Affiliate ID " . $affiliateId, $is_test);

	// Update the conversion stats
	if(!is_test)
		tiaop_increment_ac($amount);

	// Delete this entry now that it was processed
	tiaop_delete_current_ip();
}

?>