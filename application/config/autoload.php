<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array();
$autoload['libraries'] = array('session', 'form_validation','crick','ion_auth','match','utils');
$autoload['drivers'] = array();
$autoload['helper'] = array('url','form','language');
$autoload['config'] = array();
$autoload['language'] = array('all_message','auth','ion_auth');
$autoload['model'] = array('Common_model', 'Crud_model','Jsonresult','Setting_model','Ms_model','MsAppUser_model');