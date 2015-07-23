<?php

// OptimizePress should pass false, then the paypal notification arguments
function opafti_paypal_notify ($false, $args) {
	// Delete any expired purchases
	opafti_delete_expired();

	// Don't proceed if the notification arguments weren't passed
	if(!$args) {
		if (WP_DEBUG === true) {
			error_log("opafti_paypal_notify: second argument set empty");
		}
		return;
	}

	// Don't proceed if the paypal array isn't available
	if(!$args["paypal"]) {
		if (WP_DEBUG === true) {
			error_log("opafti_paypal_notify: paypal array not found");
		}
		return;
	}

	// Show the available args in the debug file
	if (WP_DEBUG === true) {
		error_log("opafti_paypal_notify: args value = ");
		if (is_array($args) || is_object($args)) {
            error_log(print_r($args, true));
		} else {
			error_log($args);
        }
	}

	// Get the referral information
	$affiliateId = affiliate_wp()->tracking->get_affiliate_id();
	$amount = $args["paypal"]["mc_gross"];
	$description = $args["paypal"]["transaction_subject"];
	$reference= $args["paypal"]["payer_email"];
	$payerIpAddress = $args["paypal"]["option_selection2"];

	// If the affiliateId wasn't found using get_affiliate_id(), save to try later
	if( empty($affiliateId) ) {
		if(WP_DEBUG === true) {
			error_log("opafti_paypal_notify: affiliate_wp()->tracking->get_affiliate_id() did not obtain affiliateId, storing in database to try later");
		}

		// Save purchase
		opafti_save_purchase($payerIpAddress, $amount, $description, $reference);
		return;
	}

	// If the affiliateId wasn't found, log and return
	if( empty($affiliateId) ) {
		if(WP_DEBUG === true) {
			error_log("opafti_paypal_notify: affiliate id was not found");
		}
		return;
	}

	// If the affiliateId isn't valid, log and return
	if( !affiliate_wp()->tracking->is_valid_affiliate($affiliate_id) ) {
		if(WP_DEBUG === true) {
			error_log("opafti_paypal_notify: affiliate id was not valid");
		}
		return;
	}

	// Get the visit id
	$visitId = affiliate_wp()->tracking->get_visit_id();

	// Call the function that adds the referral
	opafti_add_referral($affiliateId, $amount, $description, $reference, "", $visitId);
}

// Add opafti_paypal_notify to the OptimizePress paypal processing hook
if(!has_filter("ws_plugin__optimizemember_during_paypal_notify_conditionals", "opafti_paypal_notify")) {
	add_filter("ws_plugin__optimizemember_during_paypal_notify_conditionals", "opafti_paypal_notify", 10, 2);
}