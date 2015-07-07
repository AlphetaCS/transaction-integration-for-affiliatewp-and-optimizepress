<?php

// OptimizePress should pass false, then the paypal notification arguments
function opafti_paypal_notify ($false, $args) {
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

	// If the affiliateId wasn't found, log and return
	if( empty($affiliateId) || !affiliate_wp()->tracking->is_valid_affiliate($affiliate_id) ) {
		error_log("opafti_paypal_notify: affiliate id was not found");
		return;
	}

	// Call the function that adds the referral
	opafti_add_referral($affiliateId, $amount, $description, $reference, "");
}

// Add opafti_paypal_notify to the OptimizePress paypal processing hook
if(!has_filter("ws_plugin__optimizemember_during_paypal_notify_conditionals", "opafti_paypal_notify")) {
	add_filter("ws_plugin__optimizemember_during_paypal_notify_conditionals", "opafti_paypal_notify", 10, 2);
}