<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_col_output extends CI_Migration {

	public function up(){
	
		$fields = array(
			'output' => array('type' => 'TEXT')
		);
		$this->dbforge->add_column('missions', $fields);
	
	}

	public function down(){
	
		$this->dbforge->drop_column('missions', 'output');
	
	}
	
}