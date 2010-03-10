<?php
require_once("facebook.php");
/**
 *  This class extends and modifies the "Facebook" class to better suit iframe
 *  apps in CodeIgniter. The only method that needs to be overwritten so far is validate_fb_params().
 *  This extension assumes that the app is using $_SESSION to maintain the fb_sig params.
 */
class FacebookIframe extends Facebook {
	
  public function __construct($api_key,$secret,$generate_session_secret = false) {
    parent::__construct($api_key, $secret, $generate_session_secret=false);
  }

  public function validate_fb_params($resolve_auth_token=true) {
    $this->fb_params = $this->get_valid_fb_params($_POST, 48 * 3600, 'fb_sig');

    // note that with preload FQL, it's possible to receive POST params in
    // addition to GET, so use a different prefix to differentiate them
    if (!$this->fb_params) {
      // Change $_GET to $_REQUEST, since CI strips all vars from $_GET unless
      // query strings are enabled.    	
      #$fb_params = $this->get_valid_fb_params($_GET, 48 * 3600, 'fb_sig');
      $fb_params = $this->get_valid_fb_params($_REQUEST, 48 * 3600, 'fb_sig');
      $fb_post_params = $this->get_valid_fb_params($_POST, 48 * 3600, 'fb_post_sig');
      
      // Since this is an iframe app we are using sessions to propagate the fb_sig params
      // which are loaded upon the first page load.  Therefore we need to check the 
      // session var for the fb_sig params.  Only do this is if FB is not already passing
      // in fb_sig vars via $_REQUEST, since we want to give priority to their sig vars.
      $fb_session_params = (empty($fb_params)) ? $this->get_valid_fb_params($_SESSION, 48 * 3600, 'fb_sig') : array();
      
      $this->fb_params = array_merge($fb_params, $fb_post_params, $fb_session_params);
    }

    // Okay, something came in via POST or GET
    if ($this->fb_params) {
      $user               = isset($this->fb_params['user']) ?
                            $this->fb_params['user'] : null;
      $this->profile_user = isset($this->fb_params['profile_user']) ?
                            $this->fb_params['profile_user'] : null;
      $this->canvas_user  = isset($this->fb_params['canvas_user']) ?
                            $this->fb_params['canvas_user'] : null;
      $this->base_domain  = isset($this->fb_params['base_domain']) ?
                            $this->fb_params['base_domain'] : null;
      $this->ext_perms    = isset($this->fb_params['ext_perms']) ?
                            explode(',', $this->fb_params['ext_perms'])
                            : array();

      if (isset($this->fb_params['session_key'])) {
        $session_key =  $this->fb_params['session_key'];
      } else if (isset($this->fb_params['profile_session_key'])) {
        $session_key =  $this->fb_params['profile_session_key'];
      } else {
        $session_key = null;
      }
      $expires     = isset($this->fb_params['expires']) ?
                     $this->fb_params['expires'] : null;
      $this->set_user($user,
                      $session_key,
                      $expires);
    }
    // if no Facebook parameters were found in the GET or POST variables,
    // then fall back to cookies, which may have cached user information
    // Cookies are also used to receive session data via the Javascript API
    else if ($cookies =
             $this->get_valid_fb_params($_COOKIE, null, $this->api_key)) {

      $base_domain_cookie = 'base_domain_' . $this->api_key;
      if (isset($_COOKIE[$base_domain_cookie])) {
        $this->base_domain = $_COOKIE[$base_domain_cookie];
      }

      // use $api_key . '_' as a prefix for the cookies in case there are
      // multiple facebook clients on the same domain.
      $expires = isset($cookies['expires']) ? $cookies['expires'] : null;
      $this->set_user($cookies['user'],
                      $cookies['session_key'],
                      $expires);
    }
    // finally, if we received no parameters, but the 'auth_token' GET var
    // is present, then we are in the middle of auth handshake,
    // so go ahead and create the session
    else if ($resolve_auth_token && isset($_GET['auth_token']) &&
             $session = $this->do_get_session($_GET['auth_token'])) {
      if ($this->generate_session_secret &&
          !empty($session['secret'])) {
        $session_secret = $session['secret'];
      }

      if (isset($session['base_domain'])) {
        $this->base_domain = $session['base_domain'];
      }

      $this->set_user($session['uid'],
                      $session['session_key'],
                      $session['expires'],
                      isset($session_secret) ? $session_secret : null);
    }

    return !empty($this->fb_params);
  }
} 