<?php
require_once("config.php");
require_once("libraries/facebook_test.php");
require_once("hooks/fb_require_login_test.php");

/**
 * These tests are meant to be run by PHPUnit
 */
class AllTests 
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');
        $suite->addTestSuite('facebook_test');
		$suite->addTestSuite('fb_require_login_test');
        return $suite;
    }
}
?>