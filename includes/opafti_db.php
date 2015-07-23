<?php

function opafti_get_user_ip(){
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}

function opafti_create_saved_purchase_table() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'opafti_saved_purchases';
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
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}

function opafti_save_purchase($payerIpAddress, $amount, $description, $reference) {
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'opafti_saved_purchases';

	// Expire tomorrow
	$expires = date("Y-m-d H:i:s", time() + 86400);

	// Save the purchase info
	opafti_delete_ip($payerIpAddress);
	$wpdb->insert($db_table_name, array('ip_address' => $payerIpAddress, 'amount' => $amount, 'description' => $description, 'reference' => $reference, 'expires' => $expires));
}

function opafti_get_saved_purchase() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'opafti_saved_purchases';

	// Get the ip address
	$ip_address = opafti_get_user_ip();

	// Get matching saved purchase
	return $wpdb->get_row("SELECT * FROM " . $db_table_name . " WHERE ip_address = '" . $ip_address . "'", ARRAY_N);
}

function opafti_delete_current_ip() {
	opafti_delete_ip(opafti_get_user_ip());
}

function opafti_delete_ip($ipAddress) {
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'opafti_saved_purchases';

	// Build delete syntax
	$deleteSql = "DELETE FROM $db_table_name WHERE ip_address = '$ipAddress'";

	// Run delete
	$wpdb->query($deleteSql);
}

function opafti_delete_expired() {
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'opafti_saved_purchases';

	// Build delete syntax
	$deleteSql = "DELETE FROM $db_table_name WHERE expires < NOW()";

	// Run delete
	$wpdb->query($deleteSql);
}

function opafti_create_db_tables() {
	opafti_create_saved_purchase_table();
}