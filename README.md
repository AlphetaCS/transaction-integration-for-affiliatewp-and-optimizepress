# affiliatewp-optimizepress-transaction-integration
Wordpress Plugin to Integrate AffiliateWP with OptimizePress Transactions
This WordPress plugin attempts to link OptimizePress purchases to an AffiliateWP ID using the following strategy
 - A purchase is made in OptimizePresss
 - Attempt to read affiliate id from AffiliateWP
   - This likely fails because the purchase has been redirected to paypal and the cookie isn't available
     - Purchaser identification is stored in the database
       - Database is purged of any expired transactions (currently 1 day expiration from purchase time)
     - If a user with the same IP address as a saved transaction connects and has a valid affiliate id in their cookie, the affiliate is linked to the purchase

NOTE: Currently only integrates with PayPal via OptimizePress filter: ws_plugin__optimizemember_during_paypal_notify_conditionals