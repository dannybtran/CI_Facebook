<?php 
define('BASEPATH',"/PATH/TO/CI/ROOT");
define('SYS_PATH',BASEPATH . "system/");
define('APP_PATH',SYS_PATH . "application/");

function get_instance() {
	// This must be set in the test before calling the target object.
	return $GLOBALS['CI'];
}