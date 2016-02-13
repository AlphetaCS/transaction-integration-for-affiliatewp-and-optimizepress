<?php
	require_once TIAOP_PLUGIN_DIR . "includes/tiaop_db.php";
	$ip_address = tiaop_get_user_ip();

	if (isset($_REQUEST["btnSavePurchase"])) {
		$test_results = "IP Address: " . $ip_address;
		$test_results = $test_results . "<br />Database Time: " . tiaop_get_mysql_time();
		$test_results = $test_results . "<br />Creating Saved Purchase.";
		tiaop_save_purchase($ip_address, "1.00", "Test Purchase Description", "Test Purchase Reference", true);
		$test_results = $test_results . "<br />Check Saved Purchase Table.";
	}

	if(isset($_REQUEST["btnDeletePurchase"])) {
		$test_results = "IP Address: " . $ip_address;
		$test_results = $test_results . "<br />Deleting Saved Purchase.";
		tiaop_delete_current_ip();
		tiaop_purge_history();
		$test_results = $test_results . "<br />Check Saved Purchase Table.";
	}

	if(isset($_REQUEST["btnProcessPurchase"])) {
		$test_results = "IP Address: " . $ip_address;
		$test_results = $test_results . "<br />Affiliate ID: 1";
		$test_results = $test_results . "<br />Visit ID: 2";
		
		require_once TIAOP_PLUGIN_DIR . "includes/tiaop_processing.php";
		tiaop_process_saved_purchases(true);
		$test_results = $test_results . "<br />Check History Table.";
	}
?>
<div style="margin-top:10px">
	<form method="post" action="">
		<table class="form-table">
			<tr>
				<input type="submit" id="btnSavePurchase" name="btnSavePurchase" value="Create Saved Purchase" class="button-primary" style="margin-right:5px" />
				<input type="submit" id="btnDeletePurchase" name="btnDeletePurchase" value="Delete Saved Purchase" class="button-primary" style="margin-right:5px" />
				<input type="submit" id="btnProcessPurchase" name="btnProcessPurchase" value="Process Saved Purchase" class="button-primary" style="margin-right:5px" /></td>
			</tr>
		</table>
	</form>
	<?php if($test_results) echo '<span class="description">Results: </span><div style="margin-left: 10px">' . $test_results . '</div>'; ?>
</div>