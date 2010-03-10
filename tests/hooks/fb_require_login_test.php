<?php 
if (!defined('BASEPATH')) require_once("../config.php");
require_once(APP_PATH . "hooks/fb_require_login.php");

class fb_require_login_test extends PHPUnit_Framework_TestCase {
	public function test_fb_require_login() {
		
		$app_type = "iframe";
		$api_key = "1249809dfj93f90s";
		$secret = "fj9f30s9fjdf9j393";
		
		$params = array(
			"app_type" => $app_type,
			"api_key" => $api_key,
			"secret" => $secret
		);
		
		$controller = "welcome";
		$method = "index";
		$controllers_uppercase = array("WELCOME" => array("INDEX"));
		$controllers_lowercase = array("welcome" => array("index"));
		
		$load_mock = $this->getMock("fb_require_login_loader_mock",array('config','library'));
		$load_mock->expects($this->at(0))
			->method('config')
			->with('facebook');
		$load_mock->expects($this->at(1))
			->method('library')
			->with('facebook',$params);
			
		$config_mock = $this->getMock("fb_require_login_config_mock",array('item'));
		$config_mock->expects($this->at(0))
			->method('item')
			->with('app_type')
			->will($this->returnValue($app_type));
		$config_mock->expects($this->at(1))
			->method('item')
			->with('api_key')
			->will($this->returnValue($api_key));
		$config_mock->expects($this->at(2))
			->method('item')
			->with('secret')
			->will($this->returnValue($secret));
		$config_mock->expects($this->at(3))
			->method('item')
			->with('fb_require_login_controllers')
			->will($this->returnValue($controllers_uppercase));
			
		$facebook_mock = $this->getMock("fb_require_login_facebook_mock",array('require_fb_login'));
		$facebook_mock->expects($this->once())
			->method('require_fb_login')
			->with($controller,$method,$controllers_lowercase);
			
		$router_mock = new stdClass();
		$router_mock->class = strtoupper($controller);
		$router_mock->method = strtoupper($method);

		$ci = new stdClass();
		$ci->load = $load_mock;
		$ci->config = $config_mock;
		$ci->facebook = $facebook_mock;
		$ci->router = $router_mock;
		
		$GLOBALS['CI'] = $ci;
		fb_require_login();
	}	
}