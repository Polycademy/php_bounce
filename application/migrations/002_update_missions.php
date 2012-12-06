<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_missions extends CI_Migration {

	public function up(){
	
		$fields = array(
			'mission_number' => array('type' => 'INT')
		);
		$this->dbforge->add_column('missions', $fields);
	
	}

	public function down(){
	
		$this->dbforge->drop_column('missions', 'mission_number');
	
	}
	
}