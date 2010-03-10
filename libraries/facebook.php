<?php
require_once("facebook/facebook_iframe.php");
require_once("facebook/facebook_fbml.php");
class CI_Facebook 
{
	public $fb;
	public $ci;

	public function CI_Facebook($params) 
	{
		if (!array_key_exists("app_type",$params))
			throw new Exception("Could not instantiate Facebook library. App type not found.");
		if (!array_key_exists("api_key",$params))
			throw new Exception("Could not instantiate Facebook library. No api key found.");
		if (!array_key_exists("secret",$params))
			throw new Exception("Could not instantiate Facebook library. No secret found.");
		$this->load_fb($params);
		$this->load_ci();
	}
	
	private function load_ci() 
	{
		$this->ci =& get_instance();
	}
	
	protected function load_fb($params)
	{
		switch(strtolower($params['app_type'])) {
			case "iframe":
				$this->fb = new FacebookIframe($params['api_key'],$params['secret']);
				break;
			case "fbml":
				$this->fb = new FacebookFbml($params['api_key'],$params['secret']);
				break;
			default:
				throw new Exception("Invalid app type.");
				break;
		}

	}
	
	protected function add_user_to_db($facebook_id) 
	{
		/*
		 * Insert user into the database
		 * 
		 * i.e.
		 * $this->ci->load->model("User_model","user");
		 * $this->ci->user->create(array("facebook_id"=>$facebook_id));		
		 * 
		 * Make sure your there is a unique key constraint on the
		 * `facebook_id` field.
		 */ 
	}
	
	public function require_fb_login($controller,$method,$controllers) 
	{
		// This is called by the fb_require_login hook
		// system/application/hooks/fb_require_login.php
				
		// If this page requires FB Login, then call Facebook's require_login function
		if ( array_key_exists($controller,$controllers) &&
			( 	in_array("*",$controllers[$controller]) ||
				in_array($method,$controllers[$controller]) ) ) {
		
			// Load get variables
			parse_str($_SERVER['QUERY_STRING'],$_REQUEST);
		
			// Call require_frame()
			$this->fb->require_frame();
		
			// Call require_login()
			$facebook_id = $this->fb->require_login();
			
			// Make sure user is in DB
			$this->add_user_to_db($facebook_id);
					
			// To circumvent Safari X-Browser Cookie issue, check to see if the fb_sig var
			// have been written to session yet.  If it has not, that means this is the
			// first page load and we must output an HTML form that auto-submits the fb_sig
			// params back to the same page. 
			if (!$this->ci->session->userdata('fb_sig'))
				$this->ci->load->view('facebook/require_fb_login/safari_workaround_form');
				
			//Write all fb_sig_params to session		
			foreach($_REQUEST as $k => $v) {
				if (substr($k,0,3) == "fb_") {
					$this->ci->session->set_userdata($k,$v);
				}
			}				
		}
		// Else do nothing, since this page does not require login.
		else { }		
	}
}
?>