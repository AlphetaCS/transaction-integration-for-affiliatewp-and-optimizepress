<div class="wrap">
	<h2>Transaction Integration for AffiliateWP and OptimizePress</h2>
	<hr />
	<h3>Licensing</h3>
	<div style="margin-left:3em">
		<?php
			settings_fields("tiaop-license");
			$license_key = esc_attr(get_option("tiaop_license_key", ""));

			require_once TIAOP_PLUGIN_DIR . "includes/configuration/tiaop_slm.php";

			$acs_sl = new tiaop_acs_sml();
			if (isset($_REQUEST["activate_license"]) || isset($_REQUEST["deactivate_license"])) {
				$activate = isset($_REQUEST["activate_license"]);
				$license_key = $_REQUEST["license_key"];
				$result = $acs_sl->update_license($license_key, $activate);
				$license_key = esc_attr(get_option("tiaop_license_key", ""));

				if(!$activate) {
					settings_fields("tiaop-settings");
					update_option("tiaop_expiration_value", 1);
					update_option("tiaop_retain_history_value", -1);
					update_option("tiaop_expiration_units", "days");
					update_option("tiaop_retain_history_units", "weeks");
					require_once TIAOP_PLUGIN_DIR . "includes/tiaop_db.php";
					tiaop_purge_history();
				}
			}

			$active_license = $acs_sl->check_license($license_key);
			echo '<span class="description">'; if($active_license) echo "Active license found. Thank you!"; else echo "Enter a valid license key to unlock the configuration settings below."; echo '</span>';
		?>
		<form method="post" action="">
			<table class="form-table">
				<tr>
					<th style="padding-bottom: 0"><label for="license_key">License Key</label></th>
					<?php echo '<td style="padding-bottom: 0"><input class="regular-text" type="text" id="license_key" name="license_key" placeholder="License Key" value="' . $license_key . '"'; if($active_license) echo "readonly"; echo '></td>'; ?>
				</tr>
				<tr>
					<th style="padding: 0" />
					<?php echo '<td style="padding: 0 0 0 15px"><span class="description">'; if($active_license) echo 'S'; else echo 'License keys can be purchased <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XYHMBAKNZJ5RU" target="_blank">here</a> and s'; echo 'upport is available by contacting us at <a href="mailto:tiaop_support@alphetacs.com">tiaop_support@alphetacs.com</a></span></td>'; ?>
				</tr>
				<tr>
					<th />
					<?php echo '<td>'; if(!$active_license) echo '<input type="submit" name="activate_license" value="Activate" class="button-primary" style="margin-right:5px" />'; else echo '<input type="submit" name="deactivate_license" value="Deactivate" class="button" />'; echo '</td>'; ?>
				</tr>
			</table>
		</form>
	</div>
	<hr />
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
								<input id="inpExpire" name="tiaop_expiration_value" style="width:75px" type="number" min="1" max="999" value="' . $ip_expiration_val . '"'; if(!$active_license) echo " readonly"; echo ' />
								<select id="selExpireUnits" name="tiaop_expiration_units">
									<option value="minutes"'; if($ip_expiration_units == "minutes") echo ' selected="selected"'; if(!$active_license) echo " disabled='disabled'"; echo '>Minutes</option>
									<option value="hours"'; if($ip_expiration_units == "hours") echo ' selected="selected"'; if(!$active_license) echo " disabled='disabled'"; echo '>Hours</option>
									<option value="days"'; if($ip_expiration_units == "days") echo ' selected="selected"'; echo '>Days</option>
									<option value="weeks"'; if($ip_expiration_units == "weeks") echo ' selected="selected"'; if(!$active_license) echo " disabled='disabled'"; echo '>Weeks</option>
									<option value="months"'; if($ip_expiration_units == "months") echo ' selected="selected"'; if(!$active_license) echo " disabled='disabled'"; echo '>Months</option>
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
							<?php echo '<input id="inpRetainHistoryValue" name="tiaop_retain_history_value" style="width:75px" type="number" min="-1" max="999" value="' . $rh_expiration_val . '"'; if(!$active_license) echo " readonly"; echo ' />
							<select id="selRetainHistoryUnits" name="tiaop_retain_history_units" >
								<option value="minutes"'; if($rh_expiration_units == "minutes") echo ' selected="selected"'; if(!$active_license) echo "disabled='disabled'"; echo '>Minutes</option>
								<option value="hours"'; if($rh_expiration_units == "hours") echo ' selected="selected"'; if(!$active_license) echo "disabled='disabled'"; echo '>Hours</option>
								<option value="days"'; if($rh_expiration_units == "days") echo ' selected="selected"'; if(!$active_license) echo "disabled='disabled'"; echo '>Days</option>
								<option value="weeks"'; if($rh_expiration_units == "weeks") echo ' selected="selected"'; echo '>Weeks</option>
								<option value="months"'; if($rh_expiration_units == "months") echo ' selected="selected"'; if(!$active_license) echo "disabled='disabled'"; echo '>Months</option>
							</select>'; ?>
						</span>
					</td>
					<td><span class="description">Specify the length of time that history of all purchases (and whether or not they were linked) is retained.<br>Use 0 to retain indefinitely. Use -1 to not retain a history.<br />Default: -1</span></td>
				</tr>
			</table>
		</div>
		<?php if($active_license) echo '<div style="margin-left:1em"><input type="submit" name="Submit" value="Save Changes" class="button-primary" /></div>'; ?>
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