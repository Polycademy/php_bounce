<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_missions extends CI_Migration {

	public function up(){
	
		$this->dbforge->add_field('id');
	
		$this->dbforge->add_field(array(
			'title' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
			),
			'description' => array(
				'type' => 'TEXT',
			),
			'parameters' => array(
				'type' => 'TEXT',
			),
		));

		$this->dbforge->create_table('missions');
	
	}

	public function down(){
	
		$this->dbforge->drop_table('missions');
	
	}
	
}