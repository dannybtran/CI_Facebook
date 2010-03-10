<?php
	$config['app_type'] = "iframe"; // options are "iframe" or "fbml"
	$config['api_key'] 	= "";
	$config['secret']	= "";
	
	$config['fb_require_login_controllers'] = array(
	/*
	 * The format is:
	 * "controller" => array("method1",...)
	 *
	 * Optionally you can specify an "*" to mean all methods 
	 * in a controller
	 * "controller" => array("*")
	 *
	 */
		"welcome" => array("index")
	);
	
