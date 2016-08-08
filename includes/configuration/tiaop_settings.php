<div class="wrap">
	<h2>Transaction Integration for AffiliateWP and OptimizePress</h2>
	<hr />
	<h3>Features And Support</h3>
	<div>
		<p>Have a suggestion for a new feature or need support? Contact us at <a href="mailto:tiaop_support@alphetacs.com">tiaop_support@alphetacs.com</a></p>
	</div>
	<hr />
	<?php
		$stats = tiaop_get_stats();
		$saved = $stats[1];
		$converted = $stats[2];
		$convertedTotal = $stats[3];
		if($convertedTotal > 0) { ?>
			<h3>Consider a donation</h3>
			<div>
				<p>Transaction Integration for AffiliateWP and OptimizePress has saved <?php echo $saved ?> purchases and processed <?php echo $converted ?> affiliate links for a total of $<?php echo $convertedTotal ?>!</p>
	<?php
		if($convertedTotal >= 10) {	?>
				<p>Now that we have processed the cost of a $5 donation <?php echo (floor($convertedTotal) / 5) ?> times, please consider a donation.</p> <?php } ?>
				<br />
				<div style="margin-left: 25px">
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NJ9RKAYS5Q2VQ" target="_blank"><img src="//www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="PayPal - The safer, easier way to pay online!" /></a>
				</div>
				<br />
			</div>
			<hr />
	<?php } ?>
	<h3>Configuration</h3>
	<form method="post" action="options.php">
		<?php
			settings_fields('tiaop-settings');
			$ip_expiration_val = esc_attr(get_option("tiaop_expiration_value", 1));
			$rh_expiration_val = esc_attr(get_option("tiaop_retain_history_value", -1));
			$ip_expiration_units = esc_attr(get_option("tiaop_expiration_units", "days"));
			$rh_expiration_units = esc_attr(get_option("tiaop_retain_history_units", "weeks"));
		?>
		<div style="margin-left:3em">
			<table class="form-table">
		    	<tr>
					<th><label for="inpExpire">Time Limit:</label></th>
					<td>
						<span style="white-space: nowrap;">
							<?php echo '
								<input id="inpExpire" name="tiaop_expiration_value" style="width:75px" type="number" min="1" max="999" value="' . $ip_expiration_val . '"'; echo ' />
								<select id="selExpireUnits" name="tiaop_expiration_units">
									<option value="minutes"'; if($ip_expiration_units == "minutes") echo ' selected="selected"'; echo '>Minutes</option>
									<option value="hours"'; if($ip_expiration_units == "hours") echo ' selected="selected"'; echo '>Hours</option>
									<option value="days"'; if($ip_expiration_units == "days") echo ' selected="selected"'; echo '>Days</option>
									<option value="weeks"'; if($ip_expiration_units == "weeks") echo ' selected="selected"'; echo '>Weeks</option>
									<option value="months"'; if($ip_expiration_units == "months") echo ' selected="selected"'; echo '>Months</option>
								</select>';
							?>
						</span>
					</td>
					<td><span class="description">Specify the length of time an IP address can be attached to a purchase.<br>Once this time limit is exceeded the IP address recorded with a purchase will not be linked to an affiliate.<br />Default: 1 Day</span></td>
				</tr>
				<tr>
					<th><label for="inpExpire">Retain History:</label></th>
					<td>
						<span style="white-space: nowrap;">
							<?php echo '<input id="inpRetainHistoryValue" name="tiaop_retain_history_value" style="width:75px" type="number" min="-1" max="999" value="' . $rh_expiration_val . '"'; echo ' />
							<select id="selRetainHistoryUnits" name="tiaop_retain_history_units" >
								<option value="minutes"'; if($rh_expiration_units == "minutes") echo ' selected="selected"'; echo '>Minutes</option>
								<option value="hours"'; if($rh_expiration_units == "hours") echo ' selected="selected"'; echo '>Hours</option>
								<option value="days"'; if($rh_expiration_units == "days") echo ' selected="selected"'; echo '>Days</option>
								<option value="weeks"'; if($rh_expiration_units == "weeks") echo ' selected="selected"'; echo '>Weeks</option>
								<option value="months"'; if($rh_expiration_units == "months") echo ' selected="selected"'; echo '>Months</option>
							</select>'; ?>
						</span>
					</td>
					<td><span class="description">Specify the length of time that history of all purchases (and whether or not they were linked) is retained.<br>Use 0 to retain indefinitely. Use -1 to not retain a history.<br />Default: -1</span></td>
				</tr>
			</table>
		</div>
		<div style="margin-left:1em">
			<input type="submit" name="Submit" value="Save Changes" class="button-primary" />
		</div>
	</form>
	<hr />
	<h3>Testing</h3>
	<div style="margin-left:3em; margin-bottom:2em">
		<span class="description">Use the buttons below to invoke various tests.</span>
		<?php require_once TIAOP_PLUGIN_DIR . "includes/tiaop_testing.php"; ?>
	</div>
	<hr />
	<h3>Saved Purchases</h3>
	<div style="margin-left:3em; margin-bottom:2em">
		<span class="description">List of purchases which are still eligible for an AffiliateWP link. Once the IP address connects again, the Affiliate ID will be read from their cookie and linked.</span>
	</div>
	<?php require_once TIAOP_PLUGIN_DIR . "includes/configuration/tiaop_show_sp.php"; ?>
	<hr />
	<h3>History</h3>
	<div style="margin-left:3em; margin-bottom:2em">
		<span class="description">History of actions taken by Transaction Integration for AffiliateWP and OptimizePress.</span>
	</div>
	<?php require_once TIAOP_PLUGIN_DIR . "includes/configuration/tiaop_show_sh.php"; ?>
</div>