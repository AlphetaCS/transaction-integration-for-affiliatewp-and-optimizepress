<?php

function tiaop_get_user_ip(){
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv("HTTP_X_FORWARDED_FOR"))
        return getenv("HTTP_X_FORWARDED_FOR");

    if (getenv("HTTP_CLIENT_IP"))
        return getenv("HTTP_CLIENT_IP");

    return getenv("REMOTE_ADDR");
}

function tiaop_get_mysql_time() {
	global $wpdb;
	$mysqlTime = $wpdb->get_var("SELECT NOW()");
	return $mysqlTime;
}

function tiaop_create_saved_purchase_table() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_saved_purchases";
	$charset_collate = $wpdb->get_charset_collate();

	$sql =	"CREATE TABLE $db_table_name
			(	id mediumint(9) NOT NULL AUTO_INCREMENT,
				ip_address text,
				amount float(7,2),
				description text,
				reference text,
				expires datetime,
        	PRIMARY KEY (id)	) $charset_collate;";

	if($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		dbDelta($sql);
	}
}

function tiaop_create_history_table() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_history";
	$charset_collate = $wpdb->get_charset_collate();

	$sql =	"CREATE TABLE $db_table_name
			(	id mediumint(9) NOT NULL AUTO_INCREMENT,
				ip_address text,
				amount float(7,2),
				description text,
				reference text,
				expired datetime,
				logged_at datetime,
				action_logged text,
        	PRIMARY KEY (id)	) $charset_collate;";

	if($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		dbDelta($sql);
	}
}

function tiaop_create_stats_table() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_stats";
	$charset_collate = $wpdb->get_charset_collate();

	$sql =	"CREATE TABLE $db_table_name
			(	hrd_count integer,
				as_count integer,
				ac_count integer,
				act_amount float(7,2),
				last_ac datetime,
				last_hrd datetime ) $charset_collate;";

	if($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		dbDelta($sql);
		$wpdb->insert($db_table_name, array("hrd_count" => 0, "as_count" => 0, "ac_count" => 0, "act_amount" => 0.00));
	}
}

function tiaop_add_history($payerIpAddress, $amount, $description, $reference, $expireTime, $action, $isTest = false) {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_history";

	settings_fields("tiaop-settings");
	$retain_history_value = esc_attr(get_option("tiaop_retain_history_value", "-1"));

	if(!$isTest && $retain_history_value == "-1")
		return;

	$wpdb->insert($db_table_name, array("ip_address" => $payerIpAddress, "amount" => $amount, "description" => $description, "reference" => $reference, "expired" => $expireTime, "logged_at" => tiaop_get_mysql_time(), "action_logged" => $action));

	if(!$isTest)
		tiaop_purge_history();
}

function tiaop_purge_history() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_history";

	// Get setting values
	settings_fields("tiaop-settings");
	$retain_history_value = esc_attr(get_option("tiaop_retain_history_value", "-1"));
	$rh_expiration_units = esc_attr(get_option("tiaop_retain_history_units", "weeks"));
	$rh_expiration_units = strtoupper(rtrim($rh_expiration_units, "s"));

	if($retain_history_value == "0")
		return;

	// Build delete syntax
	$deleteSql = "DELETE FROM $db_table_name";
	if($retain_history_value != "-1")
		$deleteSql = $deleteSql . " WHERE logged_at < date_sub(now(), INTERVAL " . $retain_history_value . " " . $rh_expiration_units . ")";

	// Run delete
	$deletedCount = $wpdb->query($deleteSql);

	// Update stats table
	tiaop_increment_hrd_count($deletedCount);
}

function tiaop_increment_ac($amount) {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_stats";

	// Build the update
	$updateSql = "UPDATE $db_table_name SET ac_count = ac_count + 1, act_amount = act_amount + $amount, last_ac = (SELECT NOW())";

	// Run the update
	$wpdb->query($updateSql);
}

function tiaop_increment_as() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_stats";

	// Build the update
	$updateSql = "UPDATE $db_table_name SET as_count = as_count + 1";

	// Run the update
	$wpdb->query($updateSql);
}

function tiaop_increment_hrd_count($incrementBy) {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_stats";

	// Build the update
	$updateSql = "UPDATE $db_table_name SET hrd_count = hrd_count + $incrementBy, last_hrd = (SELECT NOW())";

	// Run the update
	$wpdb->query($updateSql);
}

function tiaop_save_purchase($payerIpAddress, $amount, $description, $reference, $is_test = false) {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_saved_purchases";

	// Set expiration
	settings_fields("tiaop-settings");
	$expiration_value = esc_attr(get_option("tiaop_expiration_value", "1"));
	$expiration_units = esc_attr(get_option("tiaop_expiration_units", "days"));
	$currentDt = new DateTime(tiaop_get_mysql_time());
	$expires = date_add($currentDt, date_interval_create_from_date_string($expiration_value . " " . $expiration_units));
	$expires = $expires->format("Y\-m\-d\ h:i:s");

	// Log history
	tiaop_add_history($payerIpAddress, $amount, $description, $reference, $expires, "Saved", $is_test);

	// Save the purchase info
	tiaop_delete_ip($payerIpAddress);
	$wpdb->insert($db_table_name, array("ip_address" => $payerIpAddress, "amount" => $amount, "description" => $description, "reference" => $reference, "expires" => $expires));

	// Increment Affiliate Saved
	//if(!$is_test)
		tiaop_increment_as();
}

function tiaop_get_stats() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_stats";

	return $wpdb->get_row("SELECT * FROM " . $db_table_name, ARRAY_N);
}

function tiaop_get_saved_purchase() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_saved_purchases";

	// Get the ip address
	$ip_address = tiaop_get_user_ip();

	// Get matching saved purchase
	return $wpdb->get_row("SELECT * FROM " . $db_table_name . " WHERE ip_address = '" . $ip_address . "'", ARRAY_N);
}

function tiaop_delete_current_ip() {
	tiaop_delete_ip(tiaop_get_user_ip());
}

function tiaop_delete_ip($ipAddress) {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_saved_purchases";

	// Build delete syntax
	$deleteSql = "DELETE FROM $db_table_name WHERE ip_address = '$ipAddress'";

	// Run delete
	$wpdb->query($deleteSql);
}

function tiaop_delete_expired() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . "opafti_saved_purchases";
	$db_history_table = $wpdb->prefix . "opafti_history";

	// Log deletions
	$entries = $wpdb->get_results("SELECT * FROM $db_table_name WHERE expires < NOW()");
	foreach($entries as $row)
		tiaop_add_history($row->ip_address, $row->amount, $row->description, $row->reference, $row->expires, "Entry expired");

	// Build delete syntax
	$deleteSql = "DELETE FROM $db_table_name WHERE expires < NOW()";

	// Run delete
	$wpdb->query($deleteSql);
}

function tiaop_create_db_tables() {
	tiaop_create_saved_purchase_table();
	tiaop_create_history_table();
	tiaop_create_stats_table();
}

?>