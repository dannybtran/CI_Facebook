<?php
/*
 * system/application/hooks/fb_require_login.php
 * =============================================
 * This hook is called upon every page load `pre_controller_constructor` in the hooks config
 * system/application/config/hooks.php
 * 
 * For more info on hooks:
 * http://codeigniter.com/user_guide/general/hooks.html
 */
function fb_require_login() {
	// Grab a copy of the CI instance
	$ci =& get_instance();
	
	// Load the facebook config file
	$ci->load->config('facebook');

	// For some reason if you load the config, CI no longer auto submits it to
	// the library as it should. So we have to manually submit.
	// http://codeigniter.com/user_guide/general/creating_libraries.html
	$params = array(
		"app_type" => $ci->config->item('app_type'),
		"api_key" => $ci->config->item('api_key'),
		"secret" => $ci->config->item('secret')
	);
		
	// Load the FB library
	$ci->load->library("facebook",$params);
			
	// Determine what controller/method we are calling.
	$controller = strtolower($ci->router->class);
	$method = strtolower($ci->router->method);

	// Load fb_require controller/method array from the config/facebook.php
	// Change each key to lowercase
	$controllers = array_change_key_case($ci->config->item('fb_require_login_controllers'),CASE_LOWER);

	// Change each value to lowercase.
	foreach($controllers as $k => $v) { foreach($v as $i => $j) { $controllers[$k][$i] = strtolower($j); } }
		
	// Call the require_fb_login method
	$ci->facebook->require_fb_login($controller,$method,$controllers);
}