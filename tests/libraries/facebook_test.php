<?php
if (!defined('BASEPATH')) require_once("../config.php");
require_once(APP_PATH . "libraries/facebook.php");

class facebook_test extends PHPUnit_Framework_TestCase 
{
	public function facebook_test() {
		$GLOBALS['CI'] = null;	
	}
	
	public function test_constructor() {

		try {		
			$params = array();
			$fb = new facebook_mock($params);
			$this->fail("No exception thrown");
		} catch(Exception $e) {
			$this->assertEquals($e->getMessage(),"Could not instantiate Facebook library. App type not found.");
		}
				
		try {		
			$params = array(
				"app_type" => "iframe"
			);
			$fb = new facebook_mock($params);
			$this->fail("No exception thrown");
		} catch(Exception $e) {
			$this->assertEquals($e->getMessage(),"Could not instantiate Facebook library. No api key found.");
		}
		
		try {		
			$params = array(
				"app_type" => "iframe",
				"api_key" => "TESTAPIKEY"
			);
			$fb = new facebook_mock($params);
			$this->fail("No exception thrown");
		} catch(Exception $e) {
			$this->assertEquals($e->getMessage(),"Could not instantiate Facebook library. No secret found.");
		}
			
		$params = array(
			"app_type" => "iframe",
			"api_key" => "TESTAPIKEY",
			"secret" => "TESTSECRET"
		);
		$fb = new facebook_mock($params);
		$this->assertEquals(get_class($fb->fb),"FacebookIframe");

		$params = array(
			"app_type" => "fbml",
			"api_key" => "TESTAPIKEY",
			"secret" => "TESTSECRET"
		);
		$fb = new facebook_mock($params);
		$this->assertEquals(get_class($fb->fb),"FacebookFbml");			
	}
	
	public function test_require_fb_login__page_controller_all_methods() 
	{
		$params = array(
			"app_type" => "iframe",
			"api_key" => "TESTAPIKEY",
			"secret" => "TESTSECRET"
		);
		$facebook_id = 1123581321;
			
		$load_mock = $this->getMock('facebook_Loader_mock',array('view','model'));
		$load_mock->expects($this->once())
			->method('view')
			->with('facebook/require_fb_login/safari_workaround_form');
		$session_mock = $this->getMock('facebook_Session_mock',array('set_userdata','userdata'));
		$session_mock->expects($this->any())
			->method('set_userdata');
			
		$ci_mock = $this->getMock('facebook_ci_mock',array('session','user','load'));
		$ci_mock->session = $session_mock;
		$ci_mock->user = $user_mock;
		$ci_mock->load = $load_mock;
				
		$fb_mock = $this->getMock("facebook_iframe",array('require_login','require_frame'));
		$fb_mock->expects($this->once())
			->method('require_frame');
		$fb_mock->expects($this->once())
			->method('require_login')
			->will($this->returnValue($facebook_id));	

		$fb = new facebook_mock($params);			
		$fb->ci = $ci_mock;
		$fb->fb = $fb_mock;
			
		$controller = "welcome";
		$method = "about";
		$controllers = array(
			"welcome" => array("*")
		);
				
		$fb->require_fb_login($controller,$method,$controllers);
	}
	
	public function test_require_fb_login__page_controller_one_method() 
	{
		$params = array(
			"app_type" => "iframe",
			"api_key" => "TESTAPIKEY",
			"secret" => "TESTSECRET"
		);
		$facebook_id = 1123581321;
			
		$load_mock = $this->getMock('facebook_Loader_mock',array('view','model'));
		$load_mock->expects($this->once())
			->method('view')
			->with('facebook/require_fb_login/safari_workaround_form');
		$session_mock = $this->getMock('facebook_Session_mock',array('set_userdata','userdata'));
		$session_mock->expects($this->any())
			->method('set_userdata');
			
		$ci_mock = $this->getMock('facebook_ci_mock',array('session','user','load'));
		$ci_mock->session = $session_mock;
		$ci_mock->user = $user_mock;
		$ci_mock->load = $load_mock;
				
		$fb_mock = $this->getMock("facebook_iframe",array('require_login','require_frame'));
		$fb_mock->expects($this->once())
			->method('require_frame');
		$fb_mock->expects($this->once())
			->method('require_login')
			->will($this->returnValue($facebook_id));	

		$fb = new facebook_mock($params);			
		$fb->ci = $ci_mock;
		$fb->fb = $fb_mock;
			
		$controller = "welcome";
		$method = "about";
		$controllers = array(
			"welcome" => array("about","index")
		);
				
		$fb->require_fb_login($controller,$method,$controllers);
	}

	public function test_require_fb_login__page_controller_no_methods() 
	{
		$params = array(
			"app_type" => "iframe",
			"api_key" => "TESTAPIKEY",
			"secret" => "TESTSECRET"
		);
		$facebook_id = 1123581321;
			
		$user_mock = $this->getMock('facebook_User_model_mock',array('create'));
		$user_mock->expects($this->never())
			->method('create');			
		$load_mock = $this->getMock('facebook_Loader_mock',array('view','model'));
		$load_mock->expects($this->never())
			->method('model');
		$load_mock->expects($this->never())
			->method('view');
		$session_mock = $this->getMock('facebook_Session_mock',array('set_userdata','userdata'));
		$session_mock->expects($this->never())
			->method('set_userdata');
			
		$ci_mock = $this->getMock('facebook_ci_mock',array('session','user','load'));
		$ci_mock->session = $session_mock;
		$ci_mock->user = $user_mock;
		$ci_mock->load = $load_mock;
				
		$fb_mock = $this->getMock("facebook_iframe",array('require_login','require_frame'));
		$fb_mock->expects($this->never())
			->method('require_frame');
		$fb_mock->expects($this->never())
			->method('require_login');	

		$fb = new facebook_mock($params);			
		$fb->ci = $ci_mock;
		$fb->fb = $fb_mock;
			
		$controller = "welcome";
		$method = "about";
		$controllers = array(
			"welcome" => array("index")
		);
				
		$fb->require_fb_login($controller,$method,$controllers);
	}
}

class facebook_mock extends CI_Facebook {
	private function load_ci() { }	
	protected function add_user_to_db($facebook_id) { }
}

/**
 * Used for testing the add_user_to_db method.
 */ 
class facebook_mock_b extends CI_Facebook {
	public function facebook_mock_b() { }
	private function load_ci() { }	
	public function add_user_to_db_test($facebook_id) 
	{
		$this->add_user_to_db($facebook_id);	
	}
}
