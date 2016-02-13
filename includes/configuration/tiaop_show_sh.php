<div>
	<?php
		// Get the table name
		global $wpdb;
		$db_table_name = $wpdb->prefix . "opafti_history";

		// Set pagination specifics
		$pg = filter_input(INPUT_GET, "pg") ? absint(filter_input(INPUT_GET, "pg")) : 1;
		$rowlimit = 15;
		$offset = ($pg - 1) * $rowlimit;

		// Get total record count
		$totalRecords = $wpdb->get_var("SELECT COUNT('id') FROM $db_table_name");
		$pageCount = ceil($totalRecords / $rowlimit);

		// Run query
		$entries = $wpdb->get_results("SELECT * FROM $db_table_name ORDER BY logged_at DESC LIMIT $offset, $rowlimit");
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th>Log Time</th>
				<th>Action</th>
				<th>IP Address</th>
				<th>Purchase Amount</th>
				<th>Description</th>
				<th>Reference</th>
				<th>Expiration Time</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!$entries)
				echo "<tr><td colspan='5'>No Records Found</td></tr>";
			else
				foreach($entries as $row) {
					$id = $row->id;
					$logTime = $row->logged_at;
					$actionLogged = $row->action_logged;
					$ipAddress = $row->ip_address;
					$purchaseAmount = $row->amount;
					$description = $row->description;
					$reference = $row->reference;
					$expiresAt = $row->expired;

					echo "<tr><td>$logTime</td><td>$actionLogged</td><td>$ipAddress</td><td>$purchaseAmount</td><td>$description</td><td>$reference</td><td>$expiresAt</td></tr>";
				}
			?>
		</tbody>
	</table>
	<?php
		// Paginate
		$pageLinks = paginate_links(array(
			"base" => add_query_arg("pg", "%#%"),
			"format" => "",
			"prev_text" => __("&laquo;", "text-domain"),
			"next_text" => __("&raquo;", "text-domain"),
			"total" => $pageCount,
			"current" => $pg
		));

		if ($pageLinks)
			echo '<div class="tablenav"><div class="tablenav-pages">' . $pageLinks . '</div></div>';
	?>
</div>