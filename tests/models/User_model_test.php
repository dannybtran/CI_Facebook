<?php
if (!defined('BASEPATH')) require_once("../config.php");
require_once(SYS_PATH . "libraries/Model.php");
require_once(APP_PATH . "models/User_model.php");

class User_model_test extends PHPUnit_Framework_TestCase 
{
	public function test_load_db() 
	{
		$load = $this->getMock('User_model_loader_mock',array('database'));
		$load->expects($this->once())
			->method('database')
			->will($this->returnValue(1));
		$user = new User_model_mock(null,$load); 
		$user->load_db();		
		
	}
	
	public function test_load_from_id__no_result() 
	{
		$result = $this->getMock('User_model_result_mock',array('result'));;
		$result->expects($this->once())
			->method('result')
			->will($this->returnValue(array()));
		$db = $this->getMock('User_model_active_record_mock',array('where','get'));
		$db->expects($this->once())
			->method('where')
			->with($this->equalTo("id",1123581321));
		$db->expects($this->once())
			->method('get')
			->will($this->returnValue($result));
		$user = new User_model_mock($db,null);
		$success = $user->load_from_id(1123581321);
		$this->assertFalse($success);
		$this->assertNull($user->id);
		$this->assertNull($user->facebook_id);
	}
	
	public function test_load_from_id__with_result() 
	{
		$row = new stdClass();
		$row->id = 1123581321;
		$row->facebook_id = 987654321;
		$result = $this->getMock('User_model_result_mock',array('result'));;
		$result->expects($this->once())
			->method('result')
			->will($this->returnValue(array($row)));
		$db = $this->getMock('User_model_active_record_mock',array('where','get'));
		$db->expects($this->once())
			->method('where')
			->with($this->equalTo("id",$row->id));
		$db->expects($this->once())
			->method('get')
			->will($this->returnValue($result));
		$user = new User_model_mock($db,null);
		$success = $user->load_from_id($row->id);
		$this->assertTrue($success);
		$this->assertEquals($row->id,$user->id);
		$this->assertEquals($row->facebook_id,$user->facebook_id);
	}	
	
	public function test_load_from_facebook_id__no_result() 
	{
		$result = $this->getMock('User_model_result_mock',array('result'));;
		$result->expects($this->once())
			->method('result')
			->will($this->returnValue(array()));
		$db = $this->getMock('User_model_active_record_mock',array('where','get'));
		$db->expects($this->once())
			->method('where')
			->with($this->equalTo("facebook_id",1123581321));
		$db->expects($this->once())
			->method('get')
			->will($this->returnValue($result));
		$user = new User_model_mock($db,null);
		$success = $user->load_from_facebook_id(1123581321);
		$this->assertFalse($success);
		$this->assertNull($user->id);
		$this->assertNull($user->facebook_id);
	}	
	
	public function test_load_from_facebook_id__with_result() 
	{
		$row = new stdClass();
		$row->id = 1123581321;
		$row->facebook_id = 987654321;
		$result = $this->getMock('User_model_result_mock',array('result'));;
		$result->expects($this->once())
			->method('result')
			->will($this->returnValue(array($row)));
		$db = $this->getMock('User_model_active_record_mock',array('where','get'));
		$db->expects($this->once())
			->method('where')
			->with($this->equalTo("facebook_id",$row->facebook_id));
		$db->expects($this->once())
			->method('get')
			->will($this->returnValue($result));
		$user = new User_model_mock($db,null);
		$success = $user->load_from_facebook_id($row->facebook_id);
		$this->assertTrue($success);
		$this->assertEquals($row->id,$user->id);
		$this->assertEquals($row->facebook_id,$user->facebook_id);
	}	

	public function test_create__invalid_params() {
		$user = new User_model_mock($db,null);

		try {
			$user->create(123);
			$this->fail('Exception not thrown.');
		} catch(Exception $e) {
			$this->assertEquals($e->getMessage(),"Data must be an array.");
		}
		
		try {
			$user->create(array());
			$this->fail('Exception not thrown.');
		} catch(Exception $e) {
			$this->assertEquals($e->getMessage(),"Facebook_id required to create user entry.");
		}		
	}
	
	public function test_create__valid_params_user_nonexistant() {
		$facebook_id = 48496732938;
		$db = $this->getMock('User_model_active_record_mock',array('set','insert','insert_id'));
		$db->expects($this->at(0))
			->method('set')
			->with("facebook_id",$facebook_id);
		$db->expects($this->at(1))
			->method('set')
			->with("created","NOW()",FALSE);
		$db->expects($this->at(2))
			->method('set')
			->with("modified","NOW()",FALSE);
		$db->expects($this->once())
			->method('insert')
			->with("User");
		
		$user = new User_model_mock_b($db,null);
		$user->load_from_facebook_id_return_value = false;
		$success = $user->create(array("facebook_id"=>$facebook_id));
		$this->assertTrue($success);
	}
	
	public function test_create__valid_params_user_existant() {
		$facebook_id = 48496732938;
		$db = $this->getMock('User_model_active_record_mock',array('set','insert','insert_id'));
		$db->expects($this->never())
			->method('set');
		$db->expects($this->never())
			->method('insert');
		
		$user = new User_model_mock_b($db,null);
		$user->load_from_facebook_id_return_value = true;
		$success = $user->create(array("facebook_id"=>$facebook_id));
		$this->assertFalse($success);
	}	
}

class User_model_mock extends User_model {
	public function User_model_mock($db,$load) {
		$this->db = $db;
		$this->load = $load;
	}
}

class User_model_mock_b extends User_model {
	public $load_from_facebook_id_return_value;
	public function User_model_mock_b($db,$load) {
		$this->db = $db;
		$this->load = $load;
	}
	public function load_from_facebook_id($facebook_id) {
		return $this->load_from_facebook_id_return_value;
	}
}

?>