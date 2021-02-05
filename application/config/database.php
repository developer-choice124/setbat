<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => ms_is_local()? 'localhost':'localhost',
	'username' => ms_is_local()? 'root':'cricket',
	'password' => ms_is_local()? '':'India12#$',
	'database' => ms_is_local()? 'betcric':'cricket',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
//$db['default'] = array(
//	'dsn'	=> '',
//	'hostname' => ms_is_local()? 'localhost':'localhost',
//	'username' => ms_is_local()? 'root':'cricket',
//	'password' => ms_is_local()? 'root':'India12#$',
//	//'password' => ms_is_local()? '':'India12#$',
//	'database' => ms_is_local()? 'cricket':'cricket',
//	'dbdriver' => 'mysqli',
//	'dbprefix' => '',
//	'pconnect' => FALSE,
//	'db_debug' => (ENVIRONMENT !== 'production'),
//	'cache_on' => FALSE,
//	'cachedir' => '',
//	'char_set' => 'utf8',
//	'dbcollat' => 'utf8_general_ci',
//	'swap_pre' => '',
//	'encrypt' => FALSE,
//	'compress' => FALSE,
//	'stricton' => FALSE,
//	'failover' => array(),
//	'save_queries' => TRUE
//);