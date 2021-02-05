<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// Paypal library configuration
// ------------------------------------------------------------------------

// PayPal environment, Sandbox or Live
$config['sandbox'] = FALSE; // FALSE for live environment

// PayPal business email
$config['business'] = 'dominiche.productions2017@gmail.com';

// What is the default currency?
$config['paypal_lib_currency_code'] = 'INR';

// Where is the button located at?
$config['paypal_lib_button_path'] = 'assets/plugins/images/';

// If (and where) to log ipn response in a file
$config['paypal_lib_ipn_log'] = TRUE;
$config['paypal_lib_ipn_log_file'] = BASEPATH . 'logs/paypal_ipn.log';